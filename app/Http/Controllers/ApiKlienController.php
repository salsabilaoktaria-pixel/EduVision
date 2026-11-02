<?php

namespace App\Http\Controllers;

use App\Models\ApiKlien;
use Illuminate\Http\Request;

class ApiKlienController extends Controller
{
    public function index()
    {
        return view('api_klien.index', ['klien' => ApiKlien::orderByDesc('created_at')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:80']);
        [, $kunci] = ApiKlien::buat($request->nama);
        // Kunci asli ditampilkan SEKALI lewat flash session — setelah itu hanya hash yang tersimpan.
        return redirect()->route('apiklien.index')->with('kunci_baru', $kunci)->with('sukses', 'Kunci API dibuat. Salin sekarang, tidak akan ditampilkan lagi.');
    }

    public function toggle(ApiKlien $apiklien)
    {
        $apiklien->update(['aktif' => !$apiklien->aktif]);
        return back()->with('sukses', 'Kunci ' . ($apiklien->aktif ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    public function destroy(ApiKlien $apiklien)
    {
        $apiklien->delete();
        return back()->with('sukses', 'Kunci API dihapus.');
    }
}
