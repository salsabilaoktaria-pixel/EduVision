@extends('layouts.app')
@section('title', 'Rapor Siswa')
@section('content')
{{-- Siswa membuka rapor langsung dari dashboard (kartu "Lihat Rapor") = halaman pertama fitur ini,
     jadi kembali ke Dashboard. Admin/guru membuka dari Data Siswa = bukan halaman pertama,
     jadi tetap kembali ke halaman sebelumnya. --}}
<x-tombol-kembali :first-page="auth()->user()->isSiswa()" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 sm:p-8 max-w-3xl mx-auto kartu">
    <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-5">
        <div>
            <h2 class="text-xl font-extrabold text-ink-900">Rapor Akademik</h2>
            <p class="text-sm text-slate-500">{{ $siswa->nama }} — {{ $siswa->kelas->nama_kelas ?? '-' }} — NISN {{ $siswa->nisn }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ auth()->user()->isSiswa() ? route('rapor.pdfSaya') : route('rapor.pdf', $siswa) }}"
               class="bg-rose-500 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-rose-600 transition shadow-lg shadow-rose-500/20 flex items-center gap-2">
                📄 Cetak PDF
            </a>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-400 to-teal-600 text-white flex items-center justify-center text-xl font-extrabold">
                {{ strtoupper(substr($siswa->nama,0,1)) }}
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-indigo-50 to-teal-50 border border-indigo-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
        <span class="text-2xl">💬</span>
        <p class="text-sm text-slate-700 font-medium">{{ $motivasi }}</p>
    </div>

    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-slate-50 rounded-xl p-4 text-center">
            <p class="text-xs text-slate-500 font-semibold">Rata-rata Nilai</p>
            <p class="text-2xl font-extrabold text-ink-900">{{ $rataRata }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4 text-center">
            <p class="text-xs text-slate-500 font-semibold">Persentase Kehadiran</p>
            <p class="text-2xl font-extrabold text-ink-900">{{ $kehadiran }}%</p>
        </div>
        <div class="rounded-xl p-4 text-center
            @class([
                'bg-emerald-50 text-emerald-700' => $prediksi['warna']=='green',
                'bg-amber-50 text-amber-700' => $prediksi['warna']=='yellow',
                'bg-rose-50 text-rose-700' => $prediksi['warna']=='red',
            ])">
            <p class="text-xs font-semibold opacity-80">Prediksi</p>
            <p class="text-sm font-extrabold mt-1">{{ $prediksi['status'] }}</p>
        </div>
    </div>

    <table class="w-full text-sm">
        <thead><tr class="text-left text-slate-500 border-b border-slate-100">
            <th class="py-2 pr-4 font-semibold">Mapel</th><th class="py-2 pr-4 font-semibold">Semester</th>
            <th class="py-2 pr-4 font-semibold text-right">Tugas</th><th class="py-2 pr-4 font-semibold text-right">UTS</th>
            <th class="py-2 pr-4 font-semibold text-right">UAS</th><th class="py-2 pr-4 font-semibold text-right">Nilai Akhir</th>
        </tr></thead>
        <tbody>
        @forelse($siswa->nilai as $n)
            <tr class="border-b border-slate-50">
                <td class="py-2 pr-4 font-semibold text-ink-900">{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td class="py-2 pr-4 text-slate-600">{{ $n->semester }} {{ $n->tahun_ajaran }}</td>
                <td class="py-2 pr-4 text-right">{{ $n->nilai_tugas }}</td>
                <td class="py-2 pr-4 text-right">{{ $n->nilai_uts }}</td>
                <td class="py-2 pr-4 text-right">{{ $n->nilai_uas }}</td>
                <td class="py-2 pr-4 text-right font-bold">{{ $n->nilai_akhir }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center py-8 text-slate-400">Belum ada nilai tercatat.</td></tr>
        @endforelse
        </tbody>
    </table>

    <p class="text-xs text-slate-400 mt-6">
        *Prediksi dihitung otomatis dari rata-rata nilai & persentase kehadiran (algoritma dasar/rule-based),
        bukan hasil machine learning — gunakan sebagai bahan pertimbangan awal, bukan keputusan final.
    </p>
</div>
@endsection
