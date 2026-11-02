<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KomitePembayaran;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API untuk aplikasi pihak ketiga (proyek Komite). Hanya data minimum yang dibutuhkan untuk
 * penagihan — alamat, tanggal lahir, foto, nilai, absensi sengaja TIDAK dibuka.
 */
class DataSiswaApiController extends Controller
{
    private function ringkas(Siswa $s): array
    {
        return [
            'nisn' => $s->nisn,
            'nama' => $s->nama,
            'jenis_kelamin' => $s->jenis_kelamin,
            'kelas' => $s->kelas?->nama_kelas,
            'tingkat' => $s->kelas?->tingkat,
            'nama_wali' => $s->nama_wali,
            'no_hp_wali' => $s->no_hp_wali,
        ];
    }

    public function kelas()
    {
        return response()->json(['sukses' => true, 'data' => Kelas::withCount('siswa')->orderBy('nama_kelas')->get()
            ->map(fn ($k) => ['id' => $k->id, 'nama_kelas' => $k->nama_kelas, 'tingkat' => $k->tingkat, 'jumlah_siswa' => $k->siswa_count])]);
    }

    public function siswaIndex(Request $request)
    {
        $request->validate(['per_halaman' => 'nullable|integer|min:1|max:200', 'kelas_id' => 'nullable|integer', 'q' => 'nullable|string|max:60']);

        $hasil = Siswa::with('kelas')
            ->when($request->kelas_id, fn ($q, $v) => $q->where('kelas_id', $v))
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('nama', 'like', "%$v%")->orWhere('nisn', 'like', "%$v%")))
            ->orderBy('nama')
            ->paginate($request->integer('per_halaman', 50));

        return response()->json([
            'sukses' => true,
            'data' => collect($hasil->items())->map(fn ($s) => $this->ringkas($s))->values(),
            'halaman' => ['saat_ini' => $hasil->currentPage(), 'terakhir' => $hasil->lastPage(), 'total' => $hasil->total()],
        ]);
    }

    public function siswaShow(string $nisn)
    {
        $s = Siswa::with('kelas')->where('nisn', $nisn)->firstOrFail();
        return response()->json(['sukses' => true, 'data' => $this->ringkas($s)]);
    }

    // Status SPP siswa (hanya baca) — supaya aplikasi Komite bisa menampilkan konteks yang sama.
    public function spp(string $nisn)
    {
        $s = Siswa::where('nisn', $nisn)->firstOrFail();
        return response()->json(['sukses' => true, 'data' => $s->spp()->orderByDesc('tahun')->get()
            ->map(fn ($t) => ['bulan' => $t->bulan, 'tahun' => $t->tahun, 'jumlah' => (float) $t->jumlah, 'status' => $t->status,
                'tanggal_bayar' => $t->tanggal_bayar])]);
    }

    public function komiteIndex(string $nisn)
    {
        $s = Siswa::where('nisn', $nisn)->firstOrFail();
        return response()->json(['sukses' => true, 'data' => $s->komite()->orderByDesc('periode')->get()]);
    }

    // Aplikasi Komite mengirim/menimpa status pembayaran per siswa per periode (idempoten).
    public function komiteSimpan(Request $request, string $nisn)
    {
        $s = Siswa::where('nisn', $nisn)->firstOrFail();
        $v = $request->validate([
            'periode' => 'required|string|max:30',
            'nominal' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Cicilan'])],
            'referensi' => 'nullable|string|max:60',
            'dibayar_pada' => 'nullable|date',
        ]);

        $baru = KomitePembayaran::updateOrCreate(['siswa_id' => $s->id, 'periode' => $v['periode']], $v);

        if ($baru->wasRecentlyCreated || $baru->wasChanged('status')) {
            Notifikasi::kirim($s->user_id, 'spp', 'Komite ' . $v['periode'] . ': ' . $v['status'],
                'Status pembayaran komite sekolah diperbarui.', route('spp.saya'));
        }

        return response()->json(['sukses' => true, 'data' => $baru], $baru->wasRecentlyCreated ? 201 : 200);
    }
}
