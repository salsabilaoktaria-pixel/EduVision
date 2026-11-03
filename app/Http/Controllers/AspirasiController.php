<?php

namespace App\Http\Controllers;

use App\Models\Aspirasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AspirasiController extends Controller
{
    public function index()
    {
        // Admin/guru melihat semua aspirasi masuk
        $aspirasi = Aspirasi::with('user')->latest()->paginate(15);
        return view('aspirasi.index', compact('aspirasi'));
    }

    public function create()
    {
        return view('aspirasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'anonim' => 'nullable|boolean',
        ]);
        $validated['user_id'] = Auth::id();
        $validated['anonim'] = $request->boolean('anonim');
        $validated['status'] = 'Baru';

        Aspirasi::create($validated);

        return redirect()->route('aspirasi.saya')->with('sukses', 'Aspirasi/pengaduan berhasil dikirim.');
    }

    // Riwayat aspirasi milik user yang login
    public function saya()
    {
        $aspirasi = Aspirasi::where('user_id', Auth::id())->latest()->get();
        return view('aspirasi.saya', compact('aspirasi'));
    }

    public function tanggapi(Request $request, Aspirasi $aspirasi)
    {
        $request->validate([
            'tanggapan' => 'required|string',
            'status' => 'required|in:Baru,Diproses,Selesai',
        ]);
        $aspirasi->update($request->only('tanggapan', 'status'));
        return back()->with('sukses', 'Tanggapan berhasil dikirim.');
    }
}
