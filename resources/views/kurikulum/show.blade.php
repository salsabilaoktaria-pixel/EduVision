@extends('layouts.app')
@section('title', 'Detail Kurikulum')
@section('content')
<x-tombol-kembali :href="route('kurikulum.index')" />
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-xl font-display text-ink-900">{{ $kurikulum->nama }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $kurikulum->kode ? 'Kode ' . $kurikulum->kode . ' • ' : '' }}Berlaku sejak {{ $kurikulum->tahun_berlaku ?: '-' }}</p>
        </div>
        @if($kurikulum->aktif)<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">Kurikulum aktif</span>@endif
    </div>
    @if($kurikulum->deskripsi)<p class="text-sm text-slate-600 mt-3 max-w-3xl">{{ $kurikulum->deskripsi }}</p>@endif
    @if($kurikulum->aktif)<p class="mt-5 text-xs text-slate-500 text-right">Kurikulum aktif tidak bisa dihapus. Aktifkan kurikulum lain dulu bila ingin menghapusnya.</p>@endif
    <div class="mt-3 flex justify-end"><x-aksi besar :edit="route('kurikulum.edit', $kurikulum)" :hapus="$kurikulum->aktif ? null : route('kurikulum.destroy', $kurikulum)" konfirmasi="Hapus kurikulum ini? Mapel di dalamnya tetap ada." /></div>
</div>
<h3 class="font-display text-lg text-ink-900 mt-6 mb-3">Struktur Mata Pelajaran per Semester</h3>
<div class="grid md:grid-cols-2 gap-4">
    @foreach($perSemester as $nama => $daftar)
        <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3"><h4 class="font-display text-base text-ink-900">Semester {{ $nama }}</h4><span class="text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full px-2.5 py-0.5">{{ $daftar->count() }} mapel</span></div>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse($daftar as $m)
                    <li class="py-2 flex items-center justify-between gap-3"><span class="font-semibold text-ink-900">{{ $m->nama_mapel }}</span><span class="text-xs text-slate-500">{{ $m->guru->nama ?? 'Belum ada guru' }}</span></li>
                @empty
                    <li class="py-4 text-center text-slate-400">Belum ada mapel.</li>
                @endforelse
            </ul>
        </div>
    @endforeach
</div>
@endsection
