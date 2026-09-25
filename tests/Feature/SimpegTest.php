<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Divisi;
use App\Models\KonfigurasiSistem;
use App\Models\Absensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SimpegTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        // Buat data dasar
        $this->artisan('db:seed');
    }

    public function test_landing_page_can_be_viewed_by_each_role()
    {
        // Landing page memang bersifat publik; arahkan pengguna lewat /dashboard.
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/');
        $response->assertOk();

        $karyawan = User::where('role', 'karyawan')->first();
        $response = $this->actingAs($karyawan)->get('/');
        $response->assertOk();
    }

    public function test_absensi_requires_configured_office_location()
    {
        $karyawanUser = User::where('role', 'karyawan')->first();
        
        // Hapus konfigurasi lokasi kantor
        KonfigurasiSistem::truncate();

        $response = $this->actingAs($karyawanUser)->postJson(route('karyawan.absensi.check-lokasi'), [
            'latitude' => -5.1477,
            'longitude' => 119.4328,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['valid' => false]);
    }

    public function test_absensi_location_validation_using_haversine()
    {
        $karyawanUser = User::where('role', 'karyawan')->first();

        // Atur lokasi kantor
        $konfig = KonfigurasiSistem::first();
        $konfig->update([
            'lat_kantor' => -5.1477,
            'lng_kantor' => 119.4328,
            'radius_meter' => 100,
        ]);

        // 1. Test lokasi valid (jarak dekat, misal koordinat yang sama)
        $response = $this->actingAs($karyawanUser)->postJson(route('karyawan.absensi.check-lokasi'), [
            'latitude' => -5.1477,
            'longitude' => 119.4328,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => true]);

        // 2. Test lokasi invalid (jarak jauh, misal Jakarta ke Makassar)
        $response = $this->actingAs($karyawanUser)->postJson(route('karyawan.absensi.check-lokasi'), [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => false]);
    }

    public function test_deleting_karyawan_removes_biometric_and_manual_attendance(): void
    {
        // Aktifkan route model binding untuk memastikan instance pada URL
        // adalah karyawan yang benar.
        $this->withMiddleware();
        Storage::fake('public');

        $admin = User::where('role', 'admin')->firstOrFail();
        $karyawan = Karyawan::whereHas('user', fn ($query) => $query->where('role', 'karyawan'))->firstOrFail();
        $userId = $karyawan->user_id;

        Storage::disk('public')->put('absensi/biometrik.jpg', 'foto biometrik');
        Storage::disk('public')->put('absensi/manual.jpg', 'foto manual');

        $biometrik = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-01-05',
            'waktu_masuk' => '08:00:00',
            'status_kehadiran' => 'hadir',
            'status_face' => 'berhasil',
            'foto_masuk' => 'absensi/biometrik.jpg',
        ]);
        $manual = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-01-06',
            'waktu_masuk' => '08:00:00',
            'status_kehadiran' => 'hadir',
            'keterangan' => '[MANUAL] Diisi oleh admin',
            'foto_masuk' => 'absensi/manual.jpg',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.karyawan.destroy', $karyawan));

        $response->assertRedirect(route('admin-hrd.karyawan.index'));
        $this->assertDatabaseMissing('absensi', ['id' => $biometrik->id]);
        $this->assertDatabaseMissing('absensi', ['id' => $manual->id]);
        $this->assertDatabaseMissing('karyawan', ['id' => $karyawan->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        Storage::disk('public')->assertMissing('absensi/biometrik.jpg');
        Storage::disk('public')->assertMissing('absensi/manual.jpg');
    }

    public function test_pdf_export_uses_daily_employee_matrix_format(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $divisi = Divisi::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.laporan.export-pdf', [
            'periode_awal' => '2026-09-01',
            'periode_akhir' => '2026-09-24',
            'divisi_id' => $divisi->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-absensi-2026-09-01-2026-09-24.pdf');
    }

    public function test_manual_attendance_uses_the_date_selected_in_the_filter(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $karyawan = Karyawan::whereHas('user', fn ($query) => $query->where('role', 'karyawan'))->firstOrFail();

        $page = $this->actingAs($admin)->get(route('admin.absensi.manual.index', [
            'tanggal' => '2026-09-23',
        ]));

        $page->assertOk();
        $page->assertSee('value="2026-09-23"', false);

        $response = $this->postJson(route('admin.absensi.manual'), [
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-09-23',
            'waktu_masuk' => '07:00',
            'waktu_keluar' => '15:00',
            'status_kehadiran' => 'hadir',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', '2026-09-23')
            ->firstOrFail();
        $this->assertSame('07:00:00', $absensi->waktu_masuk);
    }
}
