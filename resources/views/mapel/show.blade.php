@extends('layouts.app')
@section('title', 'Detail Mata Pelajaran')
@section('content')
<x-tombol-kembali />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 inline-block px-3 py-1 rounded-full mb-2">{{ $mapel->kode_mapel }}</p>
            <h2 class="text-xl font-extrabold text-ink-900">{{ $mapel->nama_mapel }}</h2>
        </div>
        <x-aksi besar :edit="route('mapel.edit', $mapel)" :hapus="route('mapel.destroy', $mapel)" konfirmasi="Hapus mata pelajaran ini?" />
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mt-5">
        <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase">Guru Pengampu</p>
            <p class="font-bold text-ink-900 mt-1">{{ $mapel->guru->nama ?? '-' }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase">Kurikulum</p>
            <p class="font-bold text-ink-900 mt-1">{{ $mapel->kurikulum->nama ?? '-' }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase">Semester</p>
            <p class="font-bold text-ink-900 mt-1">{{ $mapel->semester === 'Semua' ? 'Ganjil & Genap' : $mapel->semester }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase">KKM</p>
            <p class="font-bold text-ink-900 mt-1">{{ $mapel->kkm }}</p>
        </div>
    </div>

    <div class="mt-5">
        <p class="text-xs text-slate-400 font-semibold uppercase mb-2">Diajarkan di Kelas</p>
        <div>
            @forelse($mapel->kelas as $k)
                <span class="inline-block px-3 py-1 mb-1 mr-1 rounded-full bg-indigo-50 text-indigo-600 text-sm font-semibold">{{ $k->nama_kelas }}</span>
            @empty
                <span class="text-slate-300 text-sm">Belum diatur di kelas manapun.</span>
            @endforelse
        </div>
    </div>
</div>
@endsection
