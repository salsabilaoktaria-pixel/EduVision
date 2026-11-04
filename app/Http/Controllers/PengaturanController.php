<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function edit()
    {
        $pengaturan = PengaturanSekolah::ambil();
        return view('pengaturan.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:20|max:5000',
        ]);

        // Cuma ada SATU baris pengaturan sekolah — update kalau sudah ada, buat kalau belum.
        $pengaturan = PengaturanSekolah::first();
        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            PengaturanSekolah::create($validated);
        }

        return back()->with('sukses', 'Lokasi sekolah berhasil disimpan. Absen Face ID sekarang akan mengukur jarak dari titik ini.');
    }
}
