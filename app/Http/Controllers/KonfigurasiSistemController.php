<?php

namespace App\Http\Controllers;

use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;

class KonfigurasiSistemController extends Controller
{
    public function index()
    {
        $konfigurasi = KonfigurasiSistem::getActive();
        return view('super-admin.konfigurasi.index', compact('konfigurasi'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'lat_kantor' => 'nullable|numeric|between:-90,90',
            'lng_kantor' => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:5000',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'toleransi_menit' => 'required|integer|min:0|max:120',
        ]);

        $konfigurasi = KonfigurasiSistem::getActive();

        if ($konfigurasi) {
            $konfigurasi->update(array_merge($validated, ['updated_by' => auth()->id()]));
        } else {
            KonfigurasiSistem::create(array_merge($validated, ['updated_by' => auth()->id()]));
        }

        return redirect()->route('super-admin.konfigurasi.index')
            ->with('success', 'Konfigurasi sistem berhasil disimpan.');
    }
}
