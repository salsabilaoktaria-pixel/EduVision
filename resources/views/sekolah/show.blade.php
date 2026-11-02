@extends('layouts.app')
@section('title', 'Detail Sekolah')
@section('content')
<x-tombol-kembali :href="route('sekolah.index')" />
<div class="grid lg:grid-cols-3 gap-5">
    <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6 lg:col-span-2">
        <div class="flex items-center gap-4 mb-5">
            <span class="w-16 h-16 rounded-2xl border-2 border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                @if($sekolah->logoUrl())<img src="{{ $sekolah->logoUrl() }}" class="w-full h-full object-contain p-1" alt="">@else<x-ikon nama="school" class="w-8 h-8 text-slate-400" />@endif
            </span>
            <div class="min-w-0"><h2 class="text-xl font-bold text-ink-900">{{ $sekolah->nama }}</h2><p class="text-sm text-slate-500">{{ $sekolah->jenjang }} @if($sekolah->npsn) • NPSN {{ $sekolah->npsn }} @endif</p></div>
        </div>
        <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            @foreach(['Alamat' => $sekolah->alamat, 'Kota' => $sekolah->kota, 'Telepon' => $sekolah->telepon, 'Email' => $sekolah->email, 'Kepala Sekolah' => $sekolah->kepala_sekolah, 'NIP Kepala Sekolah' => $sekolah->nip_kepala_sekolah, 'Titik Lokasi' => $sekolah->latitude . ', ' . $sekolah->longitude, 'Radius Absen' => $sekolah->radius_meter . ' m'] as $k => $v)
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $k }}</dt><dd class="font-semibold text-ink-900">{{ $v ?: '-' }}</dd></div>
            @endforeach
        </dl>
        <div class="mt-6 text-right"><x-aksi besar :edit="route('sekolah.edit', $sekolah)" :hapus="route('sekolah.destroy', $sekolah)" konfirmasi="Hapus sekolah ini BESERTA seluruh datanya?" /></div>
    </div>
    <div class="space-y-5">
        <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5">
            <h3 class="font-bold text-ink-900 mb-3">Ringkasan Data</h3>
            @foreach($statistik as $k => $v)<div class="flex justify-between text-sm py-1.5 border-b border-slate-100 last:border-0"><span class="text-slate-600">{{ $k }}</span><b>{{ $v }}</b></div>@endforeach
        </div>
        <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5">
            <h3 class="font-bold text-ink-900 mb-3">Akun Admin</h3>
            @forelse($admin as $a)<p class="text-sm"><b>{{ $a->name }}</b><br><span class="text-slate-500 text-xs">{{ $a->email }}</span></p>@empty<p class="text-sm text-slate-400">Belum ada admin.</p>@endforelse
        </div>
    </div>
</div>
@endsection
