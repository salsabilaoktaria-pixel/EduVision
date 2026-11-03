@extends('layouts.app')
@section('title', 'Hasil Absen Mapel')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-8 text-center mt-6 kartu">
    <span class="text-6xl">{{ $berhasil ? '✅' : '⚠️' }}</span>
    <p class="mt-4 font-bold text-lg {{ $berhasil ? 'text-emerald-700' : 'text-rose-600' }}">{{ $pesan }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $sesi->mataPelajaran->nama_mapel ?? '' }} — {{ $sesi->kelas->nama_kelas ?? '' }}</p>
    <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kembali ke Dashboard</a>
</div>
@endsection
