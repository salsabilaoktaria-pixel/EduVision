<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RaporController extends Controller
{
    // Rapor untuk admin/guru melihat siswa tertentu
    public function show(Siswa $siswa)
    {
        $data = $this->siapkanData($siswa);
        return view('rapor.show', $data);
    }

    // Siswa melihat rapor miliknya sendiri
    public function raporSaya()
    {
        $siswa = Auth::user()->siswa;
        $data = $this->siapkanData($siswa);
        return view('rapor.show', $data);
    }

    // Cetak PDF (admin/guru)
    public function pdf(Siswa $siswa)
    {
        $data = $this->siapkanData($siswa);
        $pdf = Pdf::loadView('rapor.pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->download('Rapor-' . str_replace(' ', '_', $siswa->nama) . '.pdf');
    }

    // Cetak PDF rapor milik sendiri (siswa)
    public function pdfSaya()
    {
        $siswa = Auth::user()->siswa;
        $data = $this->siapkanData($siswa);
        $pdf = Pdf::loadView('rapor.pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->download('Rapor-' . str_replace(' ', '_', $siswa->nama) . '.pdf');
    }

    private function siapkanData(Siswa $siswa)
    {
        $siswa->load('kelas', 'nilai.mataPelajaran');
        // Rapor per SEMESTER (yang dipilih di navbar): hanya nilai semester itu yang dicetak.
        $sem = \App\Support\Semester::terpilih();
        $nilaiSemester = $siswa->nilai->where('semester', $sem['semester'])->where('tahun_ajaran', $sem['tahun_ajaran'])->values();
        $siswa->setRelation('nilai', $nilaiSemester);
        $kehadiran = $siswa->persentaseKehadiran();
        $rataRata = round($nilaiSemester->avg('nilai_akhir') ?? 0, 2);
        $prediksi = $this->prediksiKelulusan($rataRata, $kehadiran);
        // PENTING: kalimat motivasi dulu dihitung terpisah cuma dari nilai,
        // sedangkan status prediksi dihitung dari nilai + kehadiran — akibatnya
        // bisa muncul kombinasi yang bertentangan (mis. status "Berisiko Tidak
        // Naik Kelas" tapi kalimatnya "Kerja bagus, nilai kamu sudah solid").
        // Sekarang kalimat motivasi mengikuti STATUS PREDIKSI yang sama, supaya
        // keduanya selalu sinkron dan menyebut alasan spesifiknya (nilai atau
        // kehadiran yang jadi penyebabnya).
        $motivasi = $this->kalimatMotivasi($prediksi['status'], $rataRata, $kehadiran);

        // Kop rapor memakai identitas & logo SEKOLAH siswa ini (bukan logo aplikasi), supaya tiap sekolah punya rapor sendiri.
        $sekolah = \App\Models\Sekolah::withoutGlobalScopes()->find($siswa->sekolah_id) ?? \App\Models\Sekolah::saatIni();
        $pathLogo = $sekolah->logo ? \Illuminate\Support\Facades\Storage::disk('public')->path($sekolah->logo) : null;
        if (!$pathLogo || !is_file($pathLogo)) {
            $pathLogo = public_path('img/logo-eduvision.png');
        }
        // Dompdf butuh ekstensi PHP GD untuk gambar PNG. Kalau GD belum aktif, rapor tetap dicetak (tanpa logo) agar tidak error.
        $logoData = (extension_loaded('gd') && is_file($pathLogo)) ? 'data:' . mime_content_type($pathLogo) . ';base64,' . base64_encode(file_get_contents($pathLogo)) : null;

        $semesterLabel = \App\Support\Semester::label($sem);

        return compact('siswa', 'kehadiran', 'rataRata', 'prediksi', 'motivasi', 'sekolah', 'logoData', 'semesterLabel');
    }

    /**
     * Prediksi kelulusan/kenaikan kelas — ALGORITMA SEDERHANA (rule-based),
     * bukan machine learning. Cocok untuk tugas kuliah & bisa dikembangkan
     * menjadi model ML (mis. Decision Tree / Logistic Regression) nanti,
     * misalnya dengan memanggil layanan Python terpisah via HTTP.
     */
    private function prediksiKelulusan($rataRata, $kehadiran)
    {
        if ($rataRata >= 75 && $kehadiran >= 85) {
            return ['status' => 'Berpeluang Naik Kelas / Lulus', 'warna' => 'green'];
        } elseif ($rataRata >= 65 && $kehadiran >= 75) {
            return ['status' => 'Perlu Perhatian & Bimbingan', 'warna' => 'yellow'];
        } else {
            return ['status' => 'Berisiko Tidak Naik Kelas', 'warna' => 'red'];
        }
    }

    // Kalimat motivasi otomatis — sekarang SELALU mengikuti status prediksi yang
    // sama (nilai + kehadiran), dan secara spesifik menyebut mana yang jadi
    // penyebab utamanya kalau statusnya kurang baik, supaya siswa tahu persis
    // apa yang perlu diperbaiki (bukan cuma kalimat generik berdasar nilai saja).
    private function kalimatMotivasi($statusPrediksi, $rataRata, $kehadiran)
    {
        if ($statusPrediksi === 'Berpeluang Naik Kelas / Lulus') {
            if ($rataRata >= 90) {
                return 'Prestasi belajar sangat baik dengan rata-rata nilai ' . $rataRata . ' dan kehadiran ' . $kehadiran . '%. Pertahankan konsistensi belajar.';
            }
            return 'Hasil belajar baik dengan rata-rata nilai ' . $rataRata . ' dan kehadiran ' . $kehadiran . '%. Dianjurkan menjaga konsistensi agar hasil terus meningkat.';
        }

        if ($statusPrediksi === 'Perlu Perhatian & Bimbingan') {
            return 'Rata-rata nilai ' . $rataRata . ' dan kehadiran ' . $kehadiran . '% sudah cukup, namun masih perlu ditingkatkan. Disarankan menambah waktu belajar dan berkonsultasi dengan guru mata pelajaran.';
        }

        // Status "Berisiko Tidak Naik Kelas": sebutkan penyebab yang sesuai data.
        $nilaiKurang = $rataRata < 65;
        $kehadiranKurang = $kehadiran < 75;
        if ($nilaiKurang && $kehadiranKurang) {
            $sebab = "rata-rata nilai ($rataRata) dan kehadiran ($kehadiran%) berada di bawah batas yang ditentukan";
        } elseif ($kehadiranKurang) {
            $sebab = "kehadiran ($kehadiran%) berada di bawah batas yang ditentukan, meskipun rata-rata nilai ($rataRata) sudah cukup";
        } else {
            $sebab = "rata-rata nilai ($rataRata) berada di bawah batas yang ditentukan, meskipun kehadiran ($kehadiran%) sudah baik";
        }
        return "Saat ini $sebab. Siswa disarankan mengikuti bimbingan dari guru mata pelajaran dan wali kelas.";
    }
}
