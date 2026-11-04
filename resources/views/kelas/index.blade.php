@extends('layouts.app')
@section('title', 'Kelas & Wali Kelas')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex justify-end mb-5">
        <a href="{{ route('kelas.create') }}" data-popup class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 tombol-aktif"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Kelas</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($kelas as $k)
        <div class="border-2 border-slate-200 rounded-2xl p-5 hover:shadow-md transition">
            <p class="text-xs text-slate-400 font-semibold uppercase">{{ $k->tingkat }}</p>
            <h3 class="text-lg font-extrabold text-ink-900">{{ $k->nama_kelas }}</h3>
            <p class="text-sm text-slate-500 mt-1">Wali: {{ $k->waliKelas->nama ?? '-' }}</p>
            <p class="text-sm text-slate-500">Jumlah Siswa: {{ $k->siswa->count() }}</p>
            <div class="mt-4"><x-aksi :detail="route('kelas.show', $k)" :edit="route('kelas.edit', $k)" :hapus="route('kelas.destroy', $k)" konfirmasi="Hapus kelas ini?" /></div>
        </div>
    @empty
        <p class="text-slate-400 col-span-full text-center py-10">Belum ada kelas.</p>
    @endforelse
    </div>
    <div class="mt-4">{{ $kelas->links() }}</div>
</div>
@endsection
