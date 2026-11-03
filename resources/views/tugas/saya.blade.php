@extends('layouts.app')
@section('title', 'Tugas Saya')
@section('content')
<x-tombol-kembali :first-page="true" />

<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-500">Semua tugas untuk kelas <b>{{ $siswa->kelas->nama_kelas ?? '-' }}</b></p>
        <a href="{{ route('tugas.kalender') }}" class="tombol-aktif bg-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-indigo-700 transition text-center">🗓️ Kalender</a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tugas as $t)
            @php $p = $t->pengumpulan->first(); @endphp
            <a href="{{ route('tugas.formKumpulkan', $t) }}" class="kartu border-2 border-slate-200 rounded-xl p-4 hover:border-teal-300 transition block">
                <div class="flex items-start justify-between gap-2">
                    @if(!$p)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $t->sudahLewatDeadline() ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500' }}">Belum Dikumpulkan</span>
                    @elseif($p->terlambat())
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">Terlambat</span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Sudah Dikumpulkan</span>
                    @endif
                    @if($p && $p->nilai !== null)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700">Nilai: {{ $p->nilai }}</span>
                    @endif
                </div>
                <p class="font-bold text-ink-900 mt-2">{{ $t->judul }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $t->mataPelajaran->nama_mapel ?? '-' }} &middot; {{ $t->guru->nama ?? '-' }}</p>
                <p class="text-xs text-slate-400 mt-2">Deadline: {{ $t->deadline->translatedFormat('d M Y, H:i') }}</p>
            </a>
        @empty
            <p class="col-span-full text-center py-12 text-slate-400">Belum ada tugas untuk kelasmu.</p>
        @endforelse
    </div>
</div>
@endsection
