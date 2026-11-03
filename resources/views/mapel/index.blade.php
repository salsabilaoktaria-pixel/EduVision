@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    @if(auth()->user()->isAdmin())
    <div class="flex justify-end mb-5">
        <a href="{{ route('mapel.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 tombol-aktif">+ Tambah Mapel</a>
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
                        @forelse($m->kelas as $k)
                            <span class="inline-block px-2 py-0.5 mb-1 mr-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold">{{ $k->nama_kelas }}</span>
                        @empty
                            <span class="text-slate-300 text-xs">Belum diatur</span>
                        @endforelse
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
