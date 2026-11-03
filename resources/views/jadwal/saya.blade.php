@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <p class="text-sm text-slate-500 mb-5">Jadwal kelas <b>{{ $siswa->kelas->nama_kelas ?? '-' }}</b></p>

    @forelse($jadwal as $hari => $daftar)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-700 bg-indigo-50 inline-block px-3 py-1 rounded-full mb-3">{{ $hari }}</h3>
            <div class="space-y-2">
                @foreach($daftar as $j)
                    <div class="flex items-center gap-3 border-2 border-slate-200 rounded-xl p-3">
                        <div class="w-16 h-12 rounded-lg bg-teal-100 text-teal-700 flex flex-col items-center justify-center font-bold text-xs shrink-0 leading-tight">
                            <span>Jam {{ $j->labelJamKe() }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-ink-900 truncate">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $j->guru->nama ?? '-' }} &middot; {{ $j->labelJamClock() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center py-12 text-slate-400">Belum ada jadwal untuk kelasmu.</p>
    @endforelse
</div>
@endsection
