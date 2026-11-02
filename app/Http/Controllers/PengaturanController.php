<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Pengaturan SEKOLAH SENDIRI (admin sekolah): identitas, logo untuk kop rapor, dan titik lokasi Face ID. */
class PengaturanController extends Controller
{
    public function edit()
    {
        $pengaturan = Sekolah::saatIni();
        return view('pengaturan.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:20',
            'jenjang' => 'required|in:SD,SMP,SMA,SMK,MA',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:30',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:20|max:5000',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $sekolah = Sekolah::saatIni();
        abort_unless($sekolah->exists, 404, 'Akun ini tidak terhubung ke sekolah mana pun.');

        if ($request->hasFile('logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logo-sekolah', 'public');
        } else {
            unset($validated['logo']);
        }

        $sekolah->update($validated);

        return back()->with('sukses', 'Pengaturan sekolah berhasil disimpan.');
    }
}
