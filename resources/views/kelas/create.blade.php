@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <form method="POST" action="{{ route('kelas.store') }}" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Nama Kelas</label><input type="text" name="nama_kelas" placeholder="X IPA 1" value="{{ old('nama_kelas') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Tingkat</label><input type="text" name="tingkat" placeholder="X / XI / XII" value="{{ old('tingkat') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Wali Kelas</label>
            <select name="wali_kelas_id" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan</button>
            <a href="{{ route('kelas.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
