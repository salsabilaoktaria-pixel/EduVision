<?php

namespace App\Http\Controllers;

use App\Support\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $custom = \App\Models\SemesterSekolah::get()->keyBy(fn ($r) => $r->semester . '|' . $r->tahun_ajaran);
        $opsi = Semester::opsi();
        $aktif = Semester::aktif();
        return view('semester.index', compact('opsi', 'custom', 'aktif'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
        ], ['tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2027/2028.']);
        [$a, $b] = array_map('intval', explode('/', $data['tahun_ajaran']));
        if ($b !== $a + 1) {
            return back()->withInput()->withErrors(['tahun_ajaran' => 'Tahun ajaran harus berurutan, misalnya 2027/2028.']);
        }
        \App\Models\SemesterSekolah::firstOrCreate($data);
        $request->session()->put(Semester::SESI, Semester::kunci($data));
        return redirect()->route('semester.index')->with('sukses', 'Semester ' . $data['semester'] . ' ' . $data['tahun_ajaran'] . ' ditambahkan dan sekarang dipilih.');
    }

    // Tambah satu semester tepat setelah semester terbaru yang ada (Ganjil -> Genap tahun ajaran sama, Genap -> Ganjil tahun ajaran berikutnya).
    public function berikutnya(Request $request)
    {
        $akhir = Semester::opsi()[0];
        [$a, $b] = array_map('intval', explode('/', $akhir['tahun_ajaran']));
        $baru = $akhir['semester'] === 'Ganjil'
            ? ['semester' => 'Genap', 'tahun_ajaran' => $a . '/' . $b]
            : ['semester' => 'Ganjil', 'tahun_ajaran' => $b . '/' . ($b + 1)];
        \App\Models\SemesterSekolah::firstOrCreate($baru);
        $request->session()->put(Semester::SESI, Semester::kunci($baru));
        return redirect()->route('semester.index')->with('sukses', 'Semester ' . $baru['semester'] . ' ' . $baru['tahun_ajaran'] . ' ditambahkan dan sekarang dipilih.');
    }

    public function destroy(\App\Models\SemesterSekolah $semesterSekolah)
    {
        $semesterSekolah->delete();
        return back()->with('sukses', 'Semester tambahan dihapus dari daftar (data di dalamnya tidak dihapus).');
    }

    public function pilih(Request $request)
    {
        $kunci = (string) $request->input('semester');
        $sah = collect(Semester::opsi())->map(fn ($s) => Semester::kunci($s))->all();
        if (in_array($kunci, $sah, true)) {
            $request->session()->put(Semester::SESI, $kunci);
        }
        return back();
    }
}
