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
        $kehadiran = $siswa->persentaseKehadiran();
        $rataRata = $siswa->rataRataNilai();
        $prediksi = $this->prediksiKelulusan($rataRata, $kehadiran);
        // PENTING: kalimat motivasi dulu dihitung terpisah cuma dari nilai,
        // sedangkan status prediksi dihitung dari nilai + kehadiran — akibatnya
        // bisa muncul kombinasi yang bertentangan (mis. status "Berisiko Tidak
        // Naik Kelas" tapi kalimatnya "Kerja bagus, nilai kamu sudah solid").
        // Sekarang kalimat motivasi mengikuti STATUS PREDIKSI yang sama, supaya
        // keduanya selalu sinkron dan menyebut alasan spesifiknya (nilai atau
        // kehadiran yang jadi penyebabnya).
        $motivasi = $this->kalimatMotivasi($prediksi['status'], $rataRata, $kehadiran);

        return compact('siswa', 'kehadiran', 'rataRata', 'prediksi', 'motivasi');
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
                return 'Luar biasa! Nilai dan kehadiranmu sama-sama sangat baik — pertahankan prestasi cemerlang ini dan terus jadi inspirasi buat teman-temanmu. 🌟';
            }
            return 'Kerja bagus! Nilai (' . $rataRata . ') dan kehadiran (' . $kehadiran . '%) kamu sudah solid, terus konsisten supaya makin naik lagi ya. 💪';
        }

        if ($statusPrediksi === 'Perlu Perhatian & Bimbingan') {
            return 'Kamu sudah di jalur yang benar (nilai ' . $rataRata . ', kehadiran ' . $kehadiran . '%), tapi masih ada ruang untuk berkembang. Tingkatkan lagi belajarnya, sedikit usaha ekstra akan sangat membantu! 📚';
        }

        // Status "Berisiko Tidak Naik Kelas" — sebutkan spesifik penyebabnya
        // (nilai, kehadiran, atau keduanya) supaya kalimatnya benar-benar
        // relevan dengan data yang ditampilkan, bukan kalimat generik.
        $nilaiKurang = $rataRata < 65;
        $kehadiranKurang = $kehadiran < 75;
        if ($nilaiKurang && $kehadiranKurang) {
            $sebab = "nilai rata-rata kamu ($rataRata) dan tingkat kehadiran kamu ($kehadiran%) masih di bawah batas aman";
        } elseif ($kehadiranKurang) {
            $sebab = "tingkat kehadiran kamu ($kehadiran%) masih di bawah batas aman, walau nilai kamu ($rataRata) sudah cukup baik";
        } else {
            $sebab = "nilai rata-rata kamu ($rataRata) masih di bawah batas aman, walau kehadiran kamu ($kehadiran%) sudah baik";
        }
        return "Jangan menyerah! Saat ini $sebab. Yuk minta bimbingan guru/wali kelas dan mulai perbaiki sedikit demi sedikit mulai sekarang. 🔥";
    }
}
