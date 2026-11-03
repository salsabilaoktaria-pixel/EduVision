@extends('layouts.app')
@section('title', 'Monitor Absensi & Lokasi Siswa')
@section('content')
<x-tombol-kembali :first-page="true" />

<div x-data="{ modalFoto: null }" @keydown.escape.window="modalFoto = null">

<form method="GET" class="mb-5 flex flex-wrap gap-2 items-center">
    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
    <select name="kelas_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        <option value="">-- Semua Kelas --</option>
        @foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
    </select>
    <span class="text-sm text-slate-500 self-center">{{ $absensiPerKelas->flatten()->count() }} absensi masuk hari ini</span>
</form>

{{-- Dipisah PER KELAS supaya tidak campur aduk --}}
@forelse($absensiPerKelas as $namaKelas => $daftar)
<div class="mb-8">
    <h3 class="text-xs font-bold uppercase tracking-widest text-teal-700 bg-teal-50 inline-block px-3 py-1 rounded-full mb-3">{{ $namaKelas }}</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($daftar as $a)
        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 overflow-hidden kartu">
            <div class="relative">
                @if($a->foto_wajah)
                    <img src="{{ asset('storage/'.$a->foto_wajah) }}"
                         @click="modalFoto = '{{ asset('storage/'.$a->foto_wajah) }}'"
                         class="w-full h-40 object-cover cursor-pointer">
                @else
                    <div class="w-full h-40 bg-slate-100 flex items-center justify-center text-slate-300">Tidak ada foto</div>
                @endif
                <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-bold
                    {{ $a->terverifikasi ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                    {{ $a->terverifikasi ? '✓ Terverifikasi' : '⚠ Perlu Cek' }}
                </span>
            </div>
            <div class="p-4">
                <p class="font-bold text-ink-900">{{ $a->siswa->nama ?? '-' }}</p>
                <p class="text-xs text-slate-500 mb-2">{{ $a->siswa->kelas->nama_kelas ?? '-' }} • Absen {{ \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') }} WIB • Ket: <b>{{ $a->status }}</b></p>
                @if($a->keterangan_detail)
                    <p class="text-xs text-slate-400 italic mb-2">"{{ $a->keterangan_detail }}"</p>
                @endif

                @if($a->latitude && $a->longitude)
                    <a href="{{ $a->tautanGoogleMaps() }}" target="_blank"
                       class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">
                       📍 Lihat lokasi di Google Maps
                    </a>
                    <p class="text-xs text-slate-400 mt-1">
                        @if(!is_null($a->jarak_ke_sekolah_meter))
                            Jarak dari sekolah: <b>{{ number_format($a->jarak_ke_sekolah_meter, 0) }} meter</b>
                            {{ $a->di_area_sekolah ? '(di dalam area sekolah)' : '(di luar area sekolah)' }}
                        @endif
                    </p>
                @else
                    <p class="text-xs text-slate-400">Lokasi tidak tersedia</p>
                @endif

                @if(!$a->terverifikasi)
                <form method="POST" action="{{ route('absensi.verifikasi', $a) }}" class="mt-3">
                    @csrf
                    <button class="w-full bg-teal-600 text-white rounded-lg px-3 py-2 text-xs font-bold hover:bg-teal-700 transition">Verifikasi Manual</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
    <p class="text-center py-16 text-slate-400">Belum ada siswa yang absen di tanggal ini.</p>
@endforelse

<div x-show="modalFoto" x-cloak @click.self="modalFoto = null"
     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <img :src="modalFoto" class="w-full rounded-2xl shadow-2xl">
        <button @click="modalFoto = null" class="mt-3 mx-auto block bg-white/90 rounded-xl px-5 py-2 text-sm font-bold">Tutup</button>
    </div>
</div>
</div>
@endsection
