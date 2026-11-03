@extends('layouts.app')
@section('title', 'Riwayat Aspirasi Saya')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="mb-4"><a href="{{ route('aspirasi.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">+ Kirim Aspirasi Baru</a></div>
<div class="space-y-4">
@forelse($aspirasi as $a)
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
        <div class="flex justify-between items-start">
            <h3 class="font-bold text-ink-900">{{ $a->judul }}</h3>
            <span class="px-2 py-1 rounded-full text-xs font-semibold
                @class(['bg-amber-100 text-amber-700' => $a->status=='Baru', 'bg-blue-100 text-blue-700' => $a->status=='Diproses', 'bg-emerald-100 text-emerald-700' => $a->status=='Selesai'])">
                {{ $a->status }}
            </span>
        </div>
        <p class="text-sm text-slate-600 mt-2">{{ $a->isi }}</p>
        @if($a->tanggapan)
            <div class="mt-3 bg-slate-50 rounded-xl p-3 text-sm text-slate-600"><b>Tanggapan Sekolah:</b> {{ $a->tanggapan }}</div>
        @endif
    </div>
@empty
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-10 text-center text-slate-400 kartu">Kamu belum pernah mengirim aspirasi.</div>
@endforelse
</div>
@endsection
