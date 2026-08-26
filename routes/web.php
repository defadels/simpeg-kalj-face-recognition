<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminHrdController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\CutiIzinController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KonfigurasiSistemController;
use Illuminate\Support\Facades\Route;


// In routes/web.php or a dedicated cache management route file
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear'); // Clear config cache as well
    Artisan::call('route:clear'); // Clear route cache
    Artisan::call('view:clear'); // Clear view cache
    Artisan::call('optimize:clear'); // Clear view cache

    return 'Cache cleared!';
});

Route::get('/run-storage-link', function () {
    Artisan::call('storage:link');
    return 'The [storage:link] command has been executed successfully.';
});


Route::get('/key-generate', function() {
    Artisan::call('key:generate');
    $output = Artisan::output();

    return nl2br($output);
});

Route::get('/seeder', function (){
    Artisan::call('db:seed');
    return 'Database seeded!';
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
    $output = Artisan::output();

    if (str_contains($output, 'Nothing to migrate')) {
        return 'Tidak ada yang di Migrate';
    }

    return nl2br($output);
});


// Landing Page (Halaman Utama)
Route::get('/', function () {
    return view('welcome');
})->name('landing');


// Generic dashboard route alias
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('karyawan.dashboard');
})->middleware('auth')->name('dashboard');

// ===================== ADMIN (TERPADU) =====================
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management (Dialihkan ke Data Karyawan)
    Route::get('/users', fn() => redirect()->route('admin.karyawan.index'))->name('users.index');
    Route::get('/users/create', fn() => redirect()->route('admin.karyawan.create'))->name('users.create');
    Route::post('/users', fn() => redirect()->route('admin.karyawan.index'))->name('users.store');
    Route::get('/users/{user}/edit', fn() => redirect()->route('admin.karyawan.index'))->name('users.edit');
    Route::put('/users/{user}', fn() => redirect()->route('admin.karyawan.index'))->name('users.update');
    Route::patch('/users/{user}/toggle-status', fn() => redirect()->route('admin.karyawan.index'))->name('users.toggle-status');
    Route::delete('/users/{user}', fn() => redirect()->route('admin.karyawan.index'))->name('users.destroy');

    // Konfigurasi Sistem
    Route::get('/konfigurasi', [KonfigurasiSistemController::class, 'index'])->name('konfigurasi.index');
    Route::put('/konfigurasi', [KonfigurasiSistemController::class, 'update'])->name('konfigurasi.update');

    // Jabatan
    Route::get('/jabatan', [AdminHrdController::class, 'indexJabatan'])->name('jabatan.index');
    Route::get('/jabatan/create', [AdminHrdController::class, 'createJabatan'])->name('jabatan.create');
    Route::post('/jabatan', [AdminHrdController::class, 'storeJabatan'])->name('jabatan.store');
    Route::get('/jabatan/{jabatan}/edit', [AdminHrdController::class, 'editJabatan'])->name('jabatan.edit');
    Route::put('/jabatan/{jabatan}', [AdminHrdController::class, 'updateJabatan'])->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [AdminHrdController::class, 'destroyJabatan'])->name('jabatan.destroy');

    // Divisi
    Route::get('/divisi', [AdminHrdController::class, 'indexDivisi'])->name('divisi.index');
    Route::get('/divisi/create', [AdminHrdController::class, 'createDivisi'])->name('divisi.create');
    Route::post('/divisi', [AdminHrdController::class, 'storeDivisi'])->name('divisi.store');
    Route::get('/divisi/{divisi}/edit', [AdminHrdController::class, 'editDivisi'])->name('divisi.edit');
    Route::put('/divisi/{divisi}', [AdminHrdController::class, 'updateDivisi'])->name('divisi.update');
    Route::delete('/divisi/{divisi}', [AdminHrdController::class, 'destroyDivisi'])->name('divisi.destroy');

    // Karyawan
    Route::get('/karyawan', [AdminHrdController::class, 'indexKaryawan'])->name('karyawan.index');
    Route::get('/karyawan/create', [AdminHrdController::class, 'createKaryawan'])->name('karyawan.create');
    Route::post('/karyawan', [AdminHrdController::class, 'storeKaryawan'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}', [AdminHrdController::class, 'showKaryawan'])->name('karyawan.show');
    Route::get('/karyawan/{karyawan}/edit', [AdminHrdController::class, 'editKaryawan'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [AdminHrdController::class, 'updateKaryawan'])->name('karyawan.update');
    Route::patch('/karyawan/{karyawan}/toggle-status', [AdminHrdController::class, 'toggleStatus'])->name('karyawan.toggle-status');
    Route::delete('/karyawan/{karyawan}', [AdminHrdController::class, 'destroyKaryawan'])->name('karyawan.destroy');

    // Face Enrollment
    Route::get('/karyawan/{karyawan}/face-enrollment', [AdminHrdController::class, 'faceEnrollment'])->name('karyawan.face-enrollment');
    Route::post('/karyawan/{karyawan}/face-descriptor', [AdminHrdController::class, 'storeFaceDescriptor'])->name('karyawan.face-descriptor');

    // Monitor Absensi
    Route::get('/absensi/monitor', [AbsensiController::class, 'monitor'])->name('absensi.monitor');
    Route::get('/absensi/monitor/{absensi}', [AbsensiController::class, 'showMonitorDetail'])->name('absensi.detail');

    // Absensi Manual
    Route::get('/absensi/manual', [AbsensiController::class, 'absensiManualIndex'])->name('absensi.manual.index');
    Route::get('/absensi/belum-absen', [AbsensiController::class, 'karyawanBelumAbsen'])->name('absensi.belum-absen');
    Route::post('/absensi/manual', [AbsensiController::class, 'absensiManual'])->name('absensi.manual');

    // Approval Cuti/Izin
    Route::get('/cuti-izin', [CutiIzinController::class, 'indexApproval'])->name('cuti-izin.index');
    Route::get('/cuti-izin/{cutiIzin}', [CutiIzinController::class, 'showApproval'])->name('cuti-izin.show');
    Route::patch('/cuti-izin/{cutiIzin}/approve', [CutiIzinController::class, 'approve'])->name('cuti-izin.approve');
    Route::patch('/cuti-izin/{cutiIzin}/reject', [CutiIzinController::class, 'reject'])->name('cuti-izin.reject');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/cuti-izin', [LaporanController::class, 'cutiIzinReport'])->name('laporan.cuti-izin');
});

// ===================== KARYAWAN =====================
Route::middleware(['auth', 'check.role:karyawan,admin'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboardController::class, 'dashboard'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/check-lokasi', [AbsensiController::class, 'checkLokasi'])->name('absensi.check-lokasi');
    Route::post('/absensi/proses', [AbsensiController::class, 'prosesAbsensi'])->name('absensi.proses');
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/absensi/riwayat/{absensi}', [AbsensiController::class, 'showKaryawanDetail'])->name('absensi.detail');

    // Cuti/Izin
    Route::get('/cuti-izin', [CutiIzinController::class, 'index'])->name('cuti-izin.index');
    Route::get('/cuti-izin/create', [CutiIzinController::class, 'create'])->name('cuti-izin.create');
    Route::post('/cuti-izin', [CutiIzinController::class, 'store'])->name('cuti-izin.store');
});

// ===================== ALIAS & BACKWARD COMPATIBILITY ROUTES =====================
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    // Super Admin aliases
    Route::name('super-admin.')->prefix('super-admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', fn() => redirect()->route('admin.karyawan.index'))->name('users.index');
        Route::get('/users/create', fn() => redirect()->route('admin.karyawan.create'))->name('users.create');
        Route::post('/users', fn() => redirect()->route('admin.karyawan.index'))->name('users.store');
        Route::get('/users/{user}/edit', fn() => redirect()->route('admin.karyawan.index'))->name('users.edit');
        Route::put('/users/{user}', fn() => redirect()->route('admin.karyawan.index'))->name('users.update');
        Route::patch('/users/{user}/toggle-status', fn() => redirect()->route('admin.karyawan.index'))->name('users.toggle-status');
        Route::delete('/users/{user}', fn() => redirect()->route('admin.karyawan.index'))->name('users.destroy');
        Route::get('/konfigurasi', [KonfigurasiSistemController::class, 'index'])->name('konfigurasi.index');
        Route::put('/konfigurasi', [KonfigurasiSistemController::class, 'update'])->name('konfigurasi.update');
    });

    // Admin HRD aliases
    Route::name('admin-hrd.')->prefix('admin-hrd')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/jabatan', [AdminHrdController::class, 'indexJabatan'])->name('jabatan.index');
        Route::get('/jabatan/create', [AdminHrdController::class, 'createJabatan'])->name('jabatan.create');
        Route::post('/jabatan', [AdminHrdController::class, 'storeJabatan'])->name('jabatan.store');
        Route::get('/jabatan/{jabatan}/edit', [AdminHrdController::class, 'editJabatan'])->name('jabatan.edit');
        Route::put('/jabatan/{jabatan}', [AdminHrdController::class, 'updateJabatan'])->name('jabatan.update');
        Route::delete('/jabatan/{jabatan}', [AdminHrdController::class, 'destroyJabatan'])->name('jabatan.destroy');

        Route::get('/divisi', [AdminHrdController::class, 'indexDivisi'])->name('divisi.index');
        Route::get('/divisi/create', [AdminHrdController::class, 'createDivisi'])->name('divisi.create');
        Route::post('/divisi', [AdminHrdController::class, 'storeDivisi'])->name('divisi.store');
        Route::get('/divisi/{divisi}/edit', [AdminHrdController::class, 'editDivisi'])->name('divisi.edit');
        Route::put('/divisi/{divisi}', [AdminHrdController::class, 'updateDivisi'])->name('divisi.update');
        Route::delete('/divisi/{divisi}', [AdminHrdController::class, 'destroyDivisi'])->name('divisi.destroy');

        Route::get('/karyawan', [AdminHrdController::class, 'indexKaryawan'])->name('karyawan.index');
        Route::get('/karyawan/create', [AdminHrdController::class, 'createKaryawan'])->name('karyawan.create');
        Route::post('/karyawan', [AdminHrdController::class, 'storeKaryawan'])->name('karyawan.store');
        Route::get('/karyawan/{karyawan}', [AdminHrdController::class, 'showKaryawan'])->name('karyawan.show');
        Route::get('/karyawan/{karyawan}/edit', [AdminHrdController::class, 'editKaryawan'])->name('karyawan.edit');
        Route::put('/karyawan/{karyawan}', [AdminHrdController::class, 'updateKaryawan'])->name('karyawan.update');
        Route::patch('/karyawan/{karyawan}/toggle-status', [AdminHrdController::class, 'toggleStatus'])->name('karyawan.toggle-status');
        Route::delete('/karyawan/{karyawan}', [AdminHrdController::class, 'destroyKaryawan'])->name('karyawan.destroy');
        Route::get('/karyawan/{karyawan}/face-enrollment', [AdminHrdController::class, 'faceEnrollment'])->name('karyawan.face-enrollment');
        Route::post('/karyawan/{karyawan}/face-descriptor', [AdminHrdController::class, 'storeFaceDescriptor'])->name('karyawan.face-descriptor');

        Route::get('/absensi/monitor', [AbsensiController::class, 'monitor'])->name('absensi.monitor');
        Route::get('/absensi/monitor/{absensi}', [AbsensiController::class, 'showMonitorDetail'])->name('absensi.detail');
        Route::get('/absensi/manual', [AbsensiController::class, 'absensiManualIndex'])->name('absensi.manual.index');
        Route::get('/absensi/belum-absen', [AbsensiController::class, 'karyawanBelumAbsen'])->name('absensi.belum-absen');
        Route::post('/absensi/manual', [AbsensiController::class, 'absensiManual'])->name('absensi.manual');
        Route::get('/cuti-izin', [CutiIzinController::class, 'indexApproval'])->name('cuti-izin.index');
        Route::get('/cuti-izin/{cutiIzin}', [CutiIzinController::class, 'showApproval'])->name('cuti-izin.show');
        Route::patch('/cuti-izin/{cutiIzin}/approve', [CutiIzinController::class, 'approve'])->name('cuti-izin.approve');
        Route::patch('/cuti-izin/{cutiIzin}/reject', [CutiIzinController::class, 'reject'])->name('cuti-izin.reject');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
        Route::get('/laporan/cuti-izin', [LaporanController::class, 'cutiIzinReport'])->name('laporan.cuti-izin');
    });

    // Manajer aliases
    Route::name('manajer.')->prefix('manajer')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/cuti-izin', [CutiIzinController::class, 'indexApproval'])->name('cuti-izin.index');
        Route::get('/cuti-izin/{cutiIzin}', [CutiIzinController::class, 'showApproval'])->name('cuti-izin.show');
        Route::patch('/cuti-izin/{cutiIzin}/approve', [CutiIzinController::class, 'approve'])->name('cuti-izin.approve');
        Route::patch('/cuti-izin/{cutiIzin}/reject', [CutiIzinController::class, 'reject'])->name('cuti-izin.reject');
        Route::get('/absensi/monitor', [AbsensiController::class, 'monitor'])->name('absensi.monitor');
        Route::get('/absensi/monitor/{absensi}', [AbsensiController::class, 'showMonitorDetail'])->name('absensi.detail');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    });

    // Approval shared aliases
    Route::name('approval.')->prefix('approval')->group(function () {
        Route::get('/cuti-izin', [CutiIzinController::class, 'indexApproval'])->name('cuti-izin.index');
        Route::get('/cuti-izin/{cutiIzin}', [CutiIzinController::class, 'showApproval'])->name('cuti-izin.show');
        Route::patch('/cuti-izin/{cutiIzin}/approve', [CutiIzinController::class, 'approve'])->name('cuti-izin.approve');
        Route::patch('/cuti-izin/{cutiIzin}/reject', [CutiIzinController::class, 'reject'])->name('cuti-izin.reject');
    });
});

// Profile (semua user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
