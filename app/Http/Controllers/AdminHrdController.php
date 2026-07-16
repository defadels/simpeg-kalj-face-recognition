<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi;
use App\Models\CutiIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminHrdController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_karyawan' => Karyawan::where('status', 'aktif')->count(),
            'hadir_hari_ini' => Absensi::where('tanggal', today())->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count(),
            'cuti_pending' => CutiIzin::where('status', 'pending')->count(),
            'alpha_hari_ini' => Karyawan::where('status', 'aktif')->count() - Absensi::where('tanggal', today())->count(),
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

        return view('admin-hrd.dashboard', compact('stats', 'absensiHariIni', 'cutiPending'));
    }

    // ===================== JABATAN =====================
    public function indexJabatan(Request $request)
    {
        $jabatan = Jabatan::withCount('karyawan')
            ->when($request->search, fn($q) => $q->where('nama_jabatan', 'like', '%' . $request->search . '%'))
            ->paginate(15)->withQueryString();
        return view('admin-hrd.jabatan.index', compact('jabatan'));
    }

    public function createJabatan()
    {
        return view('admin-hrd.jabatan.create');
    }

    public function storeJabatan(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan',
            'deskripsi' => 'nullable|string',
        ]);
        Jabatan::create($validated);
        return redirect()->route('admin-hrd.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function editJabatan(Jabatan $jabatan)
    {
        return view('admin-hrd.jabatan.edit', compact('jabatan'));
    }

    public function updateJabatan(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan,' . $jabatan->id,
            'deskripsi' => 'nullable|string',
        ]);
        $jabatan->update($validated);
        return redirect()->route('admin-hrd.jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroyJabatan(Jabatan $jabatan)
    {
        if ($jabatan->karyawan()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan.');
        }
        $jabatan->delete();
        return redirect()->route('admin-hrd.jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }

    // ===================== DIVISI =====================
    public function indexDivisi(Request $request)
    {
        $divisi = Divisi::with('manajer')->withCount('karyawan')
            ->when($request->search, fn($q) => $q->where('nama_divisi', 'like', '%' . $request->search . '%'))
            ->paginate(15)->withQueryString();
        return view('admin-hrd.divisi.index', compact('divisi'));
    }

    public function createDivisi()
    {
        $karyawan = Karyawan::where('status', 'aktif')->get();
        return view('admin-hrd.divisi.create', compact('karyawan'));
    }

    public function storeDivisi(Request $request)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:divisi',
            'manajer_id' => 'nullable|exists:karyawan,id',
            'deskripsi' => 'nullable|string',
        ]);
        Divisi::create($validated);
        return redirect()->route('admin-hrd.divisi.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function editDivisi(Divisi $divisi)
    {
        $karyawan = Karyawan::where('status', 'aktif')->get();
        return view('admin-hrd.divisi.edit', compact('divisi', 'karyawan'));
    }

    public function updateDivisi(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:divisi,nama_divisi,' . $divisi->id,
            'manajer_id' => 'nullable|exists:karyawan,id',
            'deskripsi' => 'nullable|string',
        ]);
        $divisi->update($validated);
        return redirect()->route('admin-hrd.divisi.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroyDivisi(Divisi $divisi)
    {
        if ($divisi->karyawan()->count() > 0) {
            return redirect()->back()->with('error', 'Divisi tidak bisa dihapus karena masih memiliki karyawan.');
        }
        $divisi->delete();
        return redirect()->route('admin-hrd.divisi.index')->with('success', 'Divisi berhasil dihapus.');
    }

    // ===================== KARYAWAN =====================
    public function indexKaryawan(Request $request)
    {
        $karyawan = Karyawan::with(['jabatan', 'divisi', 'user'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            })
            ->when($request->divisi_id, fn($q) => $q->where('divisi_id', $request->divisi_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->paginate(15)->withQueryString();
        $divisi = Divisi::all();
        return view('admin-hrd.karyawan.index', compact('karyawan', 'divisi'));
    }

    public function createKaryawan()
    {
        $jabatan = Jabatan::all();
        $divisi = Divisi::all();
        return view('admin-hrd.karyawan.create', compact('jabatan', 'divisi'));
    }

    public function storeKaryawan(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'divisi_id' => 'nullable|exists:divisi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_masuk' => 'required|date',
            'saldo_cuti' => 'required|integer|min:0|max:365',
            'status' => 'required|in:aktif,nonaktif',
            'role' => 'required|in:manajer,karyawan',
        ]);

        // Buat user
        $user = User::create([
            'nama' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);
        $user->assignRole($validated['role']);

        // Handle foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('karyawan/foto', 'public');
        }

        Karyawan::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jabatan_id' => $validated['jabatan_id'] ?? null,
            'divisi_id' => $validated['divisi_id'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_telp' => $validated['no_telp'] ?? null,
            'foto' => $fotoPath,
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'saldo_cuti' => $validated['saldo_cuti'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin-hrd.karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function showKaryawan(Karyawan $karyawan)
    {
        $karyawan->load(['jabatan', 'divisi', 'user']);
        $absensiTerakhir = $karyawan->absensi()->latest('tanggal')->take(10)->get();
        return view('admin-hrd.karyawan.show', compact('karyawan', 'absensiTerakhir'));
    }

    public function editKaryawan(Karyawan $karyawan)
    {
        $jabatan = Jabatan::all();
        $divisi = Divisi::all();
        return view('admin-hrd.karyawan.edit', compact('karyawan', 'jabatan', 'divisi'));
    }

    public function updateKaryawan(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan,nip,' . $karyawan->id,
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $karyawan->user_id,
            'password' => 'nullable|string|min:8',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'divisi_id' => 'nullable|exists:divisi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_masuk' => 'required|date',
            'saldo_cuti' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'role' => 'required|in:manajer,karyawan',
        ]);

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('karyawan/foto', 'public');
        }

        $karyawan->update($validated);

        // Update User
        $userData = [
            'nama' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = bcrypt($validated['password']);
        }

        $karyawan->user->update($userData);
        $karyawan->user->syncRoles([$validated['role']]);

        return redirect()->route('admin-hrd.karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroyKaryawan(Karyawan $karyawan)
    {
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $karyawan->user()->delete(); // cascade ke karyawan
        return redirect()->route('admin-hrd.karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    // ===================== FACE ENROLLMENT =====================
    public function faceEnrollment(Karyawan $karyawan)
    {
        return view('admin-hrd.karyawan.face-enrollment', compact('karyawan'));
    }

    public function storeFaceDescriptor(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'face_descriptor' => 'required|array|size:128',
            'face_descriptor.*' => 'required|numeric',
        ]);

        $karyawan->update([
            'face_data' => json_encode($request->face_descriptor),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face descriptor berhasil disimpan untuk ' . $karyawan->nama_lengkap,
        ]);
    }
}
