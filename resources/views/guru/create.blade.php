@extends('layouts.app')
@section('title', 'Tambah Guru')

@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-3xl kartu">
    <form method="POST" action="{{ route('guru.store') }}" enctype="multipart/form-data" autocomplete="off" class="grid sm:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-700">NIP</label>
            <input type="text" name="nip" value="{{ old('nip') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Foto Guru</label>
            <input type="file" name="foto" accept="image/*" class="mt-1 w-full text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-slate-700">Alamat</label>
            <textarea name="alamat" rows="2" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('alamat') }}</textarea>
        </div>

        <div class="sm:col-span-2 border-t border-slate-100 pt-4 mt-2">
            <p class="text-sm font-bold text-ink-900 mb-3">Akun Login Guru</p>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off" spellcheck="false" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Password</label>
            <input type="password" name="password" minlength="8" required autocomplete="new-password" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter.</p>
            @error('password')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>

        <div class="sm:col-span-2 flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan</button>
            <a href="{{ route('guru.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
