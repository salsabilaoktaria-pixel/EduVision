@extends('layouts.app')
@section('title', 'Isi UTS & UAS')
@section('content')
<x-tombol-kembali :href="route('nilai.index')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <p class="text-sm text-slate-500 mb-1">{{ $nilai->siswa->nama }} — {{ $nilai->mataPelajaran->nama_mapel }}</p>
    <h3 class="font-bold text-ink-900 mb-4">{{ $nilai->semester }} {{ $nilai->tahun_ajaran }}</h3>

    <div class="bg-slate-50 rounded-xl p-4 mb-5 flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">Nilai Tugas (dari rata-rata tugas harian)</p>
            <p class="text-2xl font-extrabold text-ink-900">{{ $nilai->nilai_tugas }}</p>
        </div>
        <a href="{{ route('nilai.tugas', $nilai) }}" class="bg-indigo-100 text-indigo-700 rounded-xl px-4 py-2 text-xs font-bold hover:bg-indigo-200 transition">Kelola Tugas Harian</a>
    </div>

    <form method="POST" action="{{ route('nilai.update', $nilai) }}" class="space-y-4">
        @csrf @method('PUT')
        <input type="hidden" name="semester" value="{{ $nilai->semester }}">
        <input type="hidden" name="tahun_ajaran" value="{{ $nilai->tahun_ajaran }}">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Nilai UTS</label><input type="number" step="0.01" name="nilai_uts" value="{{ old('nilai_uts', $nilai->nilai_uts) }}" required min="0" max="100" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Nilai UAS</label><input type="number" step="0.01" name="nilai_uas" value="{{ old('nilai_uas', $nilai->nilai_uas) }}" required min="0" max="100" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <p class="text-xs text-slate-400">Nilai Akhir akan otomatis dihitung ulang: rata-rata dari (Nilai Tugas + UTS + UAS) ÷ 3.</p>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan & Hitung Nilai Akhir</button>
            <a href="{{ route('nilai.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
