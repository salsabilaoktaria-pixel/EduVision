@extends('layouts.app')
@section('title', 'Kalender Tugas')
@section('content')
<x-tombol-kembali :first-page="true" />

@php $user = auth()->user(); @endphp
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('tugas.kalender', ['bulan' => $bulanSebelumnya->month, 'tahun' => $bulanSebelumnya->year]) }}"
           class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-600 transition">‹</a>
        <p class="font-bold text-ink-900 text-lg">{{ $awalBulan->translatedFormat('F Y') }}</p>
        <a href="{{ route('tugas.kalender', ['bulan' => $bulanSesudahnya->month, 'tahun' => $bulanSesudahnya->year]) }}"
           class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-600 transition">›</a>
    </div>

    <div class="grid grid-cols-7 gap-1.5 text-center text-xs font-bold text-slate-400 mb-2">
        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
    </div>

    <div class="grid grid-cols-7 gap-1.5">
        @foreach($hariGrid as $hari)
            @php
                $tanggalKey = $hari->format('Y-m-d');
                $tugasHariIni = $tugasBulanIni->get($tanggalKey, collect());
                $bulanIni = $hari->month === $awalBulan->month;
                $hariIni = $hari->isToday();
            @endphp
            <div class="min-h-[70px] sm:min-h-[90px] rounded-xl border-2 p-1.5 {{ $hariIni ? 'border-teal-400 bg-teal-50/40' : 'border-slate-100' }} {{ $bulanIni ? '' : 'opacity-40' }}">
                <p class="text-xs font-bold {{ $hariIni ? 'text-teal-700' : 'text-slate-500' }}">{{ $hari->day }}</p>
                <div class="mt-1 space-y-1">
                    @foreach($tugasHariIni as $t)
                        <a href="{{ $user->isSiswa() ? route('tugas.formKumpulkan', $t) : route('tugas.show', $t) }}"
                           class="block text-[10px] sm:text-xs font-semibold px-1.5 py-1 rounded-md truncate bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition"
                           title="{{ $t->judul }} ({{ $t->mataPelajaran->nama_mapel ?? '-' }})">
                            {{ $t->judul }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
