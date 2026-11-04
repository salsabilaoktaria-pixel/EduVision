@extends('layouts.app')
@section('title', 'Ganti Password')
@section('content')
<x-tombol-kembali :first-page="true" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-lg kartu">
    <h2 class="text-lg font-extrabold text-ink-900 mb-1">Ganti Password</h2>
    <p class="text-sm text-slate-500 mb-5">Password baru akan langsung berlaku untuk login berikutnya.</p>

    @if(session('sukses'))
        <div class="mb-4 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 text-sm font-semibold rounded-xl px-4 py-3">
            {{ session('sukses') }}
        </div>
    @endif

    <form method="POST" action="{{ route('akun.updatePassword') }}" class="space-y-4" autocomplete="off">
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-700">Password Lama</label>
            <input type="password" name="password_lama" required autocomplete="current-password"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('password_lama')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Password Baru</label>
            <input type="password" name="password_baru" minlength="8" required autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter.</p>
            @error('password_baru')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
            <input type="password" name="password_baru_confirmation" minlength="8" required autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>

        <div class="pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan Password Baru</button>
        </div>
    </form>
</div>
@endsection
