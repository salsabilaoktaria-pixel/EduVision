@extends('layouts.app')
@section('title', 'Konfirmasi Password')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-md kartu">
    <h2 class="text-lg font-extrabold text-ink-900 mb-1">Konfirmasi Password Admin</h2>
    <p class="text-sm text-slate-500 mb-5">Demi keamanan, masukkan ulang password kamu sebelum melihat atau mereset akun pengguna. Berlaku 10 menit.</p>
    <form method="POST" action="{{ route('password.konfirmasi.post') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-700">Password Kamu</label>
            <input type="password" name="password" required autofocus autocomplete="current-password"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('password')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="tombol-aktif btn-utama"><x-ikon nama="check" class="w-4 h-4" /> Konfirmasi</button>
    </form>
</div>
@endsection
