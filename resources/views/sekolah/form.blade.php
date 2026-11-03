@extends('layouts.app')
@section('title', $sekolah->exists ? 'Edit Sekolah' : 'Tambah Sekolah')
@section('content')
<x-tombol-kembali :href="route('sekolah.index')" />
<form method="POST" enctype="multipart/form-data" action="{{ $sekolah->exists ? route('sekolah.update', $sekolah) : route('sekolah.store') }}" class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6 max-w-2xl space-y-4">
    @csrf @if($sekolah->exists) @method('PUT') @endif
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">Nama Sekolah</label>
            <input type="text" name="nama" required value="{{ old('nama', $sekolah->nama) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">NPSN</label>
            <input type="text" name="npsn" value="{{ old('npsn', $sekolah->npsn) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Jenjang</label>
            <select name="jenjang" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none">
                @foreach(['SD','SMP','SMA','SMK','MA'] as $j)<option value="{{ $j }}" @selected(old('jenjang', $sekolah->jenjang) === $j)>{{ $j }}</option>@endforeach
            </select></div>
        <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">Alamat</label>
            <input type="text" name="alamat" value="{{ old('alamat', $sekolah->alamat) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Kota / Kabupaten</label>
            <input type="text" name="kota" value="{{ old('kota', $sekolah->kota) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $sekolah->telepon) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Email Sekolah</label>
            <input type="email" name="email" value="{{ old('email', $sekolah->email) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Kepala Sekolah</label>
            <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah', $sekolah->kepala_sekolah) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">NIP Kepala Sekolah</label>
            <input type="text" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $sekolah->nip_kepala_sekolah) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">Logo Sekolah</label>
            <input type="file" name="logo" accept="image/png,image/jpeg" class="mt-1 block w-full text-xs text-slate-600"></div>
    </div>

    @unless($sekolah->exists)
    <div class="border-t-2 border-slate-100 pt-4">
        <h3 class="font-bold text-ink-900 mb-1">Akun Admin Sekolah</h3>
        <p class="text-xs text-slate-500 mb-3">Admin ini yang nanti mengisi data guru, siswa, kelas, dan lokasi sekolah.</p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Nama Admin</label>
                <input type="text" name="admin_nama" required value="{{ old('admin_nama') }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Email Login</label>
                <input type="email" name="admin_email" required value="{{ old('admin_email') }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">Password Awal (minimal 8 karakter)</label>
                <input type="password" name="admin_password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        </div>
    </div>
    @endunless

    <div class="flex gap-3 pt-2">
        <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold transition tombol-aktif"><x-ikon nama="check" class="w-4 h-4" /> Simpan</button>
        <a href="{{ route('sekolah.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition btn-sekunder"><x-ikon nama="x" class="w-4 h-4" /> Batal</a>
    </div>
</form>
@endsection
