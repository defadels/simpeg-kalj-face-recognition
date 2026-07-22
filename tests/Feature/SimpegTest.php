<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Divisi;
use App\Models\KonfigurasiSistem;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_login_redirects_by_role()
    {
        // 1. Test Admin redirect
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('admin.dashboard'));

        // 2. Test Karyawan redirect
        $karyawan = User::where('role', 'karyawan')->first();
        $response = $this->actingAs($karyawan)->get('/');
        $response->assertRedirect(route('karyawan.dashboard'));
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
}
