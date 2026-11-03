@extends('layouts.app')
@section('title', 'Nilai Tugas Harian')
@section('content')
<x-tombol-kembali :href="route('nilai.index')" />

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
        <h3 class="font-bold text-ink-900 mb-1">{{ $nilai->siswa->nama }}</h3>
        <p class="text-sm text-slate-500 mb-5">{{ $nilai->mataPelajaran->nama_mapel }} — {{ $nilai->semester }} {{ $nilai->tahun_ajaran }}</p>

        <table class="w-full text-sm mb-4">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-2 pr-4 font-semibold">Nama Tugas</th>
                <th class="py-2 pr-4 font-semibold">Tanggal</th>
                <th class="py-2 pr-4 font-semibold text-right">Skor</th>
                <th class="py-2 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($nilai->tugasHarian as $t)
                <tr class="border-b border-slate-50">
                    <td class="py-2 pr-4">{{ $t->nama_tugas }}</td>
                    <td class="py-2 pr-4 text-slate-500">{{ $t->tanggal ?? '-' }}</td>
                    <td class="py-2 pr-4 text-right font-semibold">{{ $t->skor }}</td>
                    <td class="py-2 pr-4 text-right">
                        <form method="POST" action="{{ route('nilai.tugas.hapus', $t) }}" onsubmit="return confirm('Hapus nilai tugas ini?')">
                            @csrf @method('DELETE')
                            <button class="px-2 py-1 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-8 text-slate-400">Belum ada nilai tugas harian. Tambahkan di form kanan.</td></tr>
            @endforelse
            </tbody>
        </table>

        <form method="POST" action="{{ route('nilai.tugas.simpan', $nilai) }}" class="flex flex-col sm:flex-row gap-2 border-t border-slate-100 pt-4">
            @csrf
            <input type="text" name="nama_tugas" placeholder="Nama tugas (Tugas 1, Kuis, dst)" required class="flex-1 rounded-xl border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <input type="number" step="0.01" name="skor" placeholder="Skor" min="0" max="100" required class="w-28 rounded-xl border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <input type="date" name="tanggal" class="rounded-xl border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <button class="bg-teal-600 text-white rounded-xl px-5 py-2 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">+ Tambah</button>
        </form>
    </div>

    <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl shadow-lg p-6 h-fit">
        <p class="text-sm font-semibold opacity-90">Rata-rata Otomatis dari {{ $nilai->tugasHarian->count() }} Tugas</p>
        <p class="text-5xl font-extrabold my-3">{{ $nilai->rataRataTugasHarian() }}</p>
        <p class="text-xs opacity-80 mb-4">Nilai Tugas yang tersimpan saat ini: <b>{{ $nilai->nilai_tugas }}</b></p>
        <form method="POST" action="{{ route('nilai.tugas.terapkan', $nilai) }}" onsubmit="return confirm('Terapkan rata-rata ini sebagai Nilai Tugas? Nilai Akhir akan dihitung ulang otomatis.')">
            @csrf
            <button class="w-full bg-white text-indigo-700 rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-indigo-50 transition">Terapkan sebagai Nilai Tugas</button>
        </form>
        <p class="text-[11px] opacity-70 mt-3">Setelah diterapkan, Nilai Akhir otomatis dihitung ulang: rata-rata dari (Nilai Tugas + Nilai UTS + Nilai UAS) ÷ 3.</p>

        <a href="{{ route('nilai.edit', $nilai) }}" class="mt-4 block text-center bg-white/15 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-white/25 transition">Lanjut Isi UTS & UAS →</a>
    </div>
</div>
@endsection
