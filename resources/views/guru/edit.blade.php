@extends('layouts.app')
@section('title', 'Edit Guru')

@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-3xl kartu">
    <form method="POST" action="{{ route('guru.update', $guru) }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
        @csrf @method('PUT')
        <div>
            <label class="text-sm font-semibold text-slate-700">NIP</label>
            <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="Laki-laki" @selected($guru->jenis_kelamin=='Laki-laki')>Laki-laki</option>
                <option value="Perempuan" @selected($guru->jenis_kelamin=='Perempuan')>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $guru->tanggal_lahir) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Foto Baru (opsional)</label>
            <input type="file" name="foto" accept="image/*" class="mt-1 w-full text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-slate-700">Alamat</label>
            <textarea name="alamat" rows="2" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('alamat', $guru->alamat) }}</textarea>
        </div>
        <div class="sm:col-span-2 flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Update</button>
            <a href="{{ route('guru.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
