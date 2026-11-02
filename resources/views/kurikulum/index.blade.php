@extends('layouts.app')
@section('title', 'Kurikulum')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <p class="text-sm text-slate-500 max-w-xl">Kurikulum yang dipakai sekolah. Tiap mata pelajaran dihubungkan ke satu kurikulum dan dikelompokkan per semester.</p>
        <a href="{{ route('kurikulum.create') }}" class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Kurikulum</a>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-slate-500 border-b border-slate-100">
            <th class="py-3 pr-4 font-semibold">Nama</th><th class="py-3 pr-4 font-semibold">Kode</th><th class="py-3 pr-4 font-semibold">Berlaku</th>
            <th class="py-3 pr-4 font-semibold">Jumlah Mapel</th><th class="py-3 pr-4 font-semibold">Status</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($kurikulum as $k)
            <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                <td class="py-3 pr-4 font-semibold text-ink-900">{{ $k->nama }}</td>
                <td class="py-3 pr-4 text-slate-600">{{ $k->kode ?: '-' }}</td>
                <td class="py-3 pr-4 text-slate-600">{{ $k->tahun_berlaku ?: '-' }}</td>
                <td class="py-3 pr-4 text-slate-600">{{ $k->mata_pelajaran_count }} mapel</td>
                <td class="py-3 pr-4">@if($k->aktif)<span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">Aktif</span>@else<span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold border border-slate-300">Tidak aktif</span>@endif</td>
                <td class="py-3 pr-4"><div class="flex justify-end"><x-aksi :detail="route('kurikulum.show', $k)" :edit="route('kurikulum.edit', $k)" :hapus="route('kurikulum.destroy', $k)" konfirmasi="Hapus kurikulum ini? Mapel di dalamnya tetap ada." /></div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center py-10 text-slate-400">Belum ada kurikulum.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
