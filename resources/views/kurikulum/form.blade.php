@extends('layouts.app')
@section('title', $kurikulum->exists ? 'Edit Kurikulum' : 'Tambah Kurikulum')
@section('content')
<x-tombol-kembali />
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6 max-w-xl">
    <form method="POST" action="{{ $kurikulum->exists ? route('kurikulum.update', $kurikulum) : route('kurikulum.store') }}" class="space-y-4">
        @csrf
        @if($kurikulum->exists) @method('PUT') @endif
        <div><label class="text-sm font-semibold text-slate-700">Nama Kurikulum</label><input type="text" name="nama" value="{{ old('nama', $kurikulum->nama) }}" required placeholder="Kurikulum Merdeka" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Kode</label><input type="text" name="kode" value="{{ old('kode', $kurikulum->kode) }}" placeholder="KM" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Mulai Berlaku</label><input type="text" name="tahun_berlaku" value="{{ old('tahun_berlaku', $kurikulum->tahun_berlaku) }}" placeholder="2022/2023" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Deskripsi</label><textarea name="deskripsi" rows="3" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('deskripsi', $kurikulum->deskripsi) }}</textarea></div>
        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $kurikulum->aktif)) class="rounded border-slate-400"> Jadikan kurikulum aktif sekolah ini</label>
        <div class="flex gap-3 pt-2">
            <button class="tombol-aktif bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition"><x-ikon nama="check" class="w-4 h-4" /> Simpan</button>
            <a href="{{ route('kurikulum.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition btn-sekunder"><x-ikon nama="x" class="w-4 h-4" /> Batal</a>
        </div>
    </form>
</div>
@endsection
