@extends('layouts.app')
@section('title', 'Data Guru')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama guru..." class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm w-56 focus:ring-2 focus:ring-teal-500 outline-none">
            <button class="bg-slate-100 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200">Cari</button>
        </form>
        <a href="{{ route('guru.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif">+ Tambah Guru</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-3 pr-4 font-semibold">NIP</th><th class="py-3 pr-4 font-semibold">Nama</th>
                <th class="py-3 pr-4 font-semibold">No HP</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($guru as $g)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-3 pr-4 font-medium">{{ $g->nip }}</td>
                    <td class="py-3 pr-4">
                        <a href="{{ route('guru.show', $g) }}" class="font-semibold text-ink-900 hover:text-teal-600">{{ $g->nama }}</a>
                    </td>
                    <td class="py-3 pr-4 text-slate-600">{{ $g->no_hp ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('guru.show', $g) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">Detail</a>
                            <a href="{{ route('guru.edit', $g) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                            <form method="POST" action="{{ route('guru.destroy', $g) }}" onsubmit="return confirm('Hapus data guru ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-10 text-slate-400">Belum ada data guru.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $guru->links() }}</div>
</div>
@endsection
