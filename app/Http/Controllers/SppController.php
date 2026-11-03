<?php

namespace App\Http\Controllers;

use App\Models\Spp;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    // Urutan bulan asli (bukan abjad) — dipakai supaya SPP terurut sesuai
    // bulan/tahun SESUNGGUHNYA, bukan berdasarkan urutan kapan datanya diinput.
    private const URUTAN_BULAN_SQL = "CASE bulan
        WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3 WHEN 'April' THEN 4
        WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6 WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8
        WHEN 'September' THEN 9 WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
        ELSE 0 END";

    public function index(Request $request)
    {
        $query = Spp::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $spp = $query->orderByDesc('tahun')->orderByRaw(self::URUTAN_BULAN_SQL . ' DESC')->get();
        $sppPerSiswa = $spp->groupBy('siswa_id');

        // Dikelompokkan per KELAS, dan di dalamnya per SISWA (bukan cuma per
        // baris tagihan) — supaya admin bisa lihat SEMUA siswa satu kelas
        // sekaligus, termasuk yang belum pernah dibuatkan tagihan SPP sama sekali.
        $siswaSemua = Siswa::with('kelas')->get();
        $siswaPerKelas = $siswaSemua
            ->sortBy(fn ($s) => sprintf(
                '%02d-%s-%s',
                $s->kelas->tingkat ?? 99,
                $s->kelas->nama_kelas ?? 'zzz',
                $s->nama ?? ''
            ))
            ->groupBy(fn ($s) => $s->kelas->nama_kelas ?? 'Tanpa Kelas');

        $siswa = $siswaSemua;
        return view('spp.index', compact('siswaPerKelas', 'sppPerSiswa', 'siswa'));
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')->get();
        return view('spp.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'jumlah' => 'required|string',
            'jumlah_custom' => 'nullable|numeric|min:0',
        ]);

        // Kalau admin pilih "Nominal lain", ambil dari kolom custom
        $nominal = $validated['jumlah'] === 'lain'
            ? $request->input('jumlah_custom', 0)
            : $validated['jumlah'];

        Spp::create([
            'siswa_id' => $validated['siswa_id'],
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'jumlah' => $nominal,
            'status' => 'Belum Lunas',
        ]);

        return redirect()->route('spp.index')->with('sukses', 'Tagihan SPP berhasil dibuat.');
    }

    public function bayar(Spp $spp)
    {
        $spp->update(['status' => 'Lunas', 'tanggal_bayar' => now()]);
        return back()->with('sukses', 'SPP ditandai sudah lunas.');
    }

    public function destroy(Spp $spp)
    {
        $spp->delete();
        return back()->with('sukses', 'Data SPP dihapus.');
    }

    // Halaman siswa melihat status SPP miliknya sendiri
    public function statusSaya()
    {
        $siswa = Auth::user()->siswa;
        $spp = Spp::where('siswa_id', $siswa->id)
            ->orderByDesc('tahun')->orderByRaw(self::URUTAN_BULAN_SQL . ' DESC')
            ->get();
        return view('spp.status', compact('spp'));
    }
}
