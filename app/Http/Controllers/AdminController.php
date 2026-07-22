<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\CutiIzin;
use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard Admin Terpadu
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'users_aktif' => User::where('is_active', true)->count(),
            'total_karyawan' => Karyawan::where('status', 'aktif')->count(),
            'hadir_hari_ini' => Absensi::where('tanggal', today())->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count(),
            'cuti_pending' => CutiIzin::where('status', 'pending')->count(),
            'alpha_hari_ini' => max(0, Karyawan::where('status', 'aktif')->count() - Absensi::where('tanggal', today())->count()),
        ];

        $absensiHariIni = Absensi::with('karyawan.divisi')
            ->where('tanggal', today())
            ->latest()
            ->take(10)
            ->get();

        $cutiPending = CutiIzin::with('karyawan')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $konfigurasi = KonfigurasiSistem::getActive();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'absensiHariIni', 'cutiPending', 'konfigurasi', 'recentUsers'));
    }

    // ===================== KELOLA PENGGUNA (USER MANAGEMENT) =====================
    public function indexUsers(Request $request)
    {
        $query = User::query();
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        if ($request->role) {
            $query->where('role', $request->role);
        }
        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,karyawan',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,karyawan',
            'is_active' => 'boolean',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Pengguna berhasil {$status}.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
