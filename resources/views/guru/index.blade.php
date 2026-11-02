@extends('layouts.app')
@section('title', 'Data Guru')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama guru..." class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm w-56 focus:ring-2 focus:ring-teal-500 outline-none">
            <button class="bg-slate-100 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200 btn-sekunder"><x-ikon nama="search" class="w-4 h-4" /> Cari</button>
            @if(request('cari'))<a href="{{ url()->current() }}" class="btn-sekunder"><x-ikon nama="x" class="w-4 h-4" /> Reset</a>@endif
        </form>
        <a href="{{ route('guru.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Guru</a>
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
                        <a href="{{ route('guru.show', $g) }}" class="flex items-center gap-3 font-semibold text-ink-900 hover:text-teal-600"><x-avatar :nama="$g->nama" :foto="$g->foto" class="w-9 h-9" teks="text-xs" />{{ $g->nama }}</a>
                    </td>
                    <td class="py-3 pr-4 text-slate-600">{{ $g->no_hp ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        <div class="flex justify-end"><x-aksi :detail="route('guru.show', $g)" :edit="route('guru.edit', $g)" :hapus="route('guru.destroy', $g)" konfirmasi="Hapus data guru ini beserta akunnya?" /></div>
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
