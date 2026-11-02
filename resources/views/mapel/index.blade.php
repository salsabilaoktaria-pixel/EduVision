@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <p class="text-sm text-slate-500">Mata pelajaran dikelompokkan per semester. Mapel berlabel "Semua" diajarkan di Ganjil dan Genap.</p>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('mapel.create') }}" class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Mapel</a>
    @endif
</div>
@foreach($perSemester as $namaSemester => $daftar)
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5 mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-display text-lg text-ink-900">Semester {{ $namaSemester }}</h2>
        <span class="text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full px-2.5 py-0.5">{{ $daftar->count() }} mapel</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-3 pr-4 font-semibold">Kode</th><th class="py-3 pr-4 font-semibold">Nama Mapel</th>
                <th class="py-3 pr-4 font-semibold">Kurikulum</th><th class="py-3 pr-4 font-semibold">Guru Pengampu</th><th class="py-3 pr-4 font-semibold">Diajarkan di Kelas</th><th class="py-3 pr-4 font-semibold">KKM</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($daftar as $m)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-3 pr-4 font-medium">{{ $m->kode_mapel }}</td>
                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $m->nama_mapel }}<span class="block text-[11px] font-semibold text-slate-500 mt-0.5">{{ $m->semester === 'Semua' ? 'Ganjil & Genap' : 'Semester ' . $m->semester }}</span></td>
                    <td class="py-3 pr-4 text-slate-600">{{ $m->kurikulum->nama ?? '-' }}</td>
                    <td class="py-3 pr-4 text-slate-600">{{ $m->guru->nama ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        @forelse($m->kelas as $k)
                            <span class="inline-block px-2 py-0.5 mb-1 mr-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold">{{ $k->nama_kelas }}</span>
                        @empty
                            <span class="text-slate-300 text-xs">Belum diatur</span>
                        @endforelse
                    </td>
                    <td class="py-3 pr-4 text-slate-600">{{ $m->kkm }}</td>
                    <td class="py-3 pr-4"><div class="flex justify-end"><x-aksi :detail="route('mapel.show', $m)" :edit="route('mapel.edit', $m)" :hapus="route('mapel.destroy', $m)" konfirmasi="Hapus mata pelajaran ini?" /></div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-8 text-slate-400">Belum ada mata pelajaran untuk semester ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection
