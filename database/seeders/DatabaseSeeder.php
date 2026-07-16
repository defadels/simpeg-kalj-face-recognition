<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Divisi;
use App\Models\KonfigurasiSistem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $roles = ['super_admin', 'admin_hrd', 'manajer', 'karyawan'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // =====================
        // 1. Super Admin
        // =====================
        $superAdmin = User::create([
            'nama' => 'Super Administrator',
            'email' => 'superadmin@sipeg.local',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // =====================
        // 2. Admin HRD
        // =====================
        $adminHrd = User::create([
            'nama' => 'Admin HRD',
            'email' => 'adminhrd@sipeg.local',
            'password' => Hash::make('password'),
            'role' => 'admin_hrd',
            'is_active' => true,
        ]);
        $adminHrd->assignRole('admin_hrd');

        // Jabatan dummy
        $jabatanHrd = Jabatan::create(['nama_jabatan' => 'Staff HRD', 'deskripsi' => 'Staf Human Resources']);
        $jabatanMgr = Jabatan::create(['nama_jabatan' => 'Manajer Produksi', 'deskripsi' => 'Manajer divisi produksi']);
        $jabatanStaff = Jabatan::create(['nama_jabatan' => 'Staff Produksi', 'deskripsi' => 'Staf produksi']);
        $jabatanFinance = Jabatan::create(['nama_jabatan' => 'Staff Finance', 'deskripsi' => 'Staf keuangan']);

        // Divisi dummy (manajer_id nullable dulu)
        $divisiProduksi = Divisi::create(['nama_divisi' => 'Produksi', 'deskripsi' => 'Divisi pengolahan hasil laut']);
        $divisiHrd = Divisi::create(['nama_divisi' => 'HRD & Umum', 'deskripsi' => 'Divisi sumber daya manusia']);
        $divisiFinance = Divisi::create(['nama_divisi' => 'Keuangan', 'deskripsi' => 'Divisi keuangan dan akuntansi']);

        // Karyawan untuk Admin HRD
        $karAdmin = Karyawan::create([
            'user_id' => $adminHrd->id,
            'nip' => 'KAL-HRD-001',
            'nama_lengkap' => 'Admin HRD',
            'jabatan_id' => $jabatanHrd->id,
            'divisi_id' => $divisiHrd->id,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-05-15',
            'alamat' => 'Jl. Bahari No. 1, Makassar',
            'no_telp' => '08110000001',
            'tanggal_masuk' => '2020-01-01',
            'saldo_cuti' => 12,
            'status' => 'aktif',
        ]);

        // =====================
        // 3. Manajer
        // =====================
        $manajer = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'manajer@sipeg.local',
            'password' => Hash::make('password'),
            'role' => 'manajer',
            'is_active' => true,
        ]);
        $manajer->assignRole('manajer');

        $karManajer = Karyawan::create([
            'user_id' => $manajer->id,
            'nip' => 'KAL-PRD-001',
            'nama_lengkap' => 'Budi Santoso',
            'jabatan_id' => $jabatanMgr->id,
            'divisi_id' => $divisiProduksi->id,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1985-03-20',
            'alamat' => 'Jl. Nelayan No. 5, Makassar',
            'no_telp' => '08110000002',
            'tanggal_masuk' => '2018-06-01',
            'saldo_cuti' => 12,
            'status' => 'aktif',
        ]);

        // Update manajer_id divisi produksi
        $divisiProduksi->update(['manajer_id' => $karManajer->id]);

        // =====================
        // 4. Karyawan biasa
        // =====================
        $karyawan = User::create([
            'nama' => 'Siti Rahayu',
            'email' => 'karyawan@sipeg.local',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'is_active' => true,
        ]);
        $karyawan->assignRole('karyawan');

        Karyawan::create([
            'user_id' => $karyawan->id,
            'nip' => 'KAL-PRD-002',
            'nama_lengkap' => 'Siti Rahayu',
            'jabatan_id' => $jabatanStaff->id,
            'divisi_id' => $divisiProduksi->id,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1995-07-10',
            'alamat' => 'Jl. Pantai No. 12, Makassar',
            'no_telp' => '08110000003',
            'tanggal_masuk' => '2022-03-01',
            'saldo_cuti' => 12,
            'status' => 'aktif',
        ]);

        // Konfigurasi sistem kosong (diisi super_admin)
        KonfigurasiSistem::create([
            'lat_kantor' => null,
            'lng_kantor' => null,
            'radius_meter' => 100,
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '17:00:00',
            'toleransi_menit' => 15,
            'updated_by' => null,
        ]);

        $this->command->info('Seeder selesai! Akun dummy:');
        $this->command->info('super_admin: superadmin@sipeg.local / password');
        $this->command->info('admin_hrd:   adminhrd@sipeg.local / password');
        $this->command->info('manajer:     manajer@sipeg.local / password');
        $this->command->info('karyawan:    karyawan@sipeg.local / password');
    }
}
