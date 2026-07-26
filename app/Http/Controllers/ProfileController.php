<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        return view('profile.edit', compact('user', 'karyawan'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user->nama = $validated['nama'];
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }
        $user->save();

        if ($karyawan) {
            $karyawanData = [
                'nama_lengkap' => $validated['nama'],
                'no_telp' => $validated['no_telp'] ?? $karyawan->no_telp,
                'alamat' => $validated['alamat'] ?? $karyawan->alamat,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? $karyawan->jenis_kelamin,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $karyawan->tanggal_lahir,
            ];

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($karyawan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($karyawan->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($karyawan->foto);
                }
                $karyawanData['foto'] = $request->file('foto')->store('karyawan-foto', 'public');
            }

            $karyawan->update($karyawanData);
        }

        return Redirect::route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
