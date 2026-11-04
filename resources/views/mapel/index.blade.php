@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    @if(auth()->user()->isAdmin())
<<<<<<< HEAD
        <a href="{{ route('mapel.create') }}" data-popup class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Mapel</a>
    @endif
</div>
@foreach($perSemester as $namaSemester => $daftar)
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5 mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-display text-lg text-ink-900">Semester {{ $namaSemester }}</h2>
        <span class="text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full px-2.5 py-0.5">{{ $daftar->count() }} mapel</span>
=======
    <div class="flex justify-end mb-5">
        <a href="{{ route('mapel.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 tombol-aktif">+ Tambah Mapel</a>
>>>>>>> fec47dc2b440329cea55aa66de621f7c0558f476
    </div>
    @endif
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-3 pr-4 font-semibold">Kode</th><th class="py-3 pr-4 font-semibold">Nama Mapel</th>
                <th class="py-3 pr-4 font-semibold">Guru Pengampu</th><th class="py-3 pr-4 font-semibold">Diajarkan di Kelas</th><th class="py-3 pr-4 font-semibold">KKM</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($mapel as $m)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-3 pr-4 font-medium">{{ $m->kode_mapel }}</td>
                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $m->nama_mapel }}</td>
                    <td class="py-3 pr-4 text-slate-600">{{ $m->guru->nama ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        @if($m->kelas->count())
                        <div class="grid grid-cols-3 gap-1 w-[12.5rem]">
                            @foreach($m->kelas as $k)
                            <span class="flex items-center justify-center h-6 rounded-full bg-indigo-50 text-indigo-800 text-[11px] font-bold whitespace-nowrap border-2 border-indigo-600">{{ $k->nama_kelas }}</span>
                            @endforeach
                        </div>
                        @else
                            <span class="text-slate-300 text-xs">Belum diatur</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 text-slate-600">{{ $m->kkm }}</td>
                    <td class="py-3 pr-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('mapel.show', $m) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">Detail</a>
                            <a href="{{ route('mapel.edit', $m) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                            <form method="POST" action="{{ route('mapel.destroy', $m) }}" onsubmit="return confirm('Hapus mapel ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-10 text-slate-400">Belum ada mata pelajaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $mapel->links() }}</div>
</div>
@endsection
