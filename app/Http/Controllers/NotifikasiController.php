<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->orderByDesc('created_at')->paginate(20);
        return view('notifikasi.index', compact('notifikasi'));
    }

    // Tandai satu notifikasi sudah dibaca lalu buka halaman tujuannya.
    public function baca(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === Auth::id(), 403);
        if (!$notifikasi->dibaca_pada) {
            $notifikasi->update(['dibaca_pada' => now()]);
        }
        return redirect($notifikasi->tautan ?: route('notifikasi.index'));
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::id())->belumDibaca()->update(['dibaca_pada' => now()]);
        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }

    // Dipanggil berkala (polling) oleh layout: jumlah belum dibaca + notifikasi terbaru untuk toast & suara.
    public function cek()
    {
        $user = Auth::user();
        \App\Support\Pengingat::jalankan($user);
        $q = Notifikasi::where('user_id', $user->id)->belumDibaca();
        $terbaru = (clone $q)->orderByDesc('id')->first();
        return response()->json([
            'jumlah' => (clone $q)->count(),
            'id_terbaru' => $terbaru->id ?? 0,
            'judul' => $terbaru->judul ?? null,
            'isi' => $terbaru->isi ?? null,
        ]);
    }
}
