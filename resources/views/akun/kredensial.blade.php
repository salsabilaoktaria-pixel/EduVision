@extends('layouts.app')
@section('title', 'Akun Pengguna')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <h2 class="text-lg font-extrabold text-ink-900">Akun Login: {{ $akun->name }}</h2>
    <p class="text-sm text-slate-500 mb-5">
        {{ $akun->role === 'siswa' ? 'Siswa' . ($akun->siswa && $akun->siswa->kelas ? ' kelas ' . $akun->siswa->kelas->nama_kelas : '') : 'Guru' }}
    </p>

    @if(session('sukses'))
        <div class="mb-4 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 text-sm font-semibold rounded-xl px-4 py-3">{{ session('sukses') }}</div>
    @endif

    <div class="rounded-xl border-2 border-slate-200 divide-y divide-slate-200 text-sm">
        <div class="flex justify-between gap-4 px-4 py-3"><span class="text-slate-500">Username (email)</span><span class="font-bold text-ink-900 break-all text-right">{{ $akun->email }}</span></div>
        <div class="flex justify-between gap-4 px-4 py-3">
            <span class="text-slate-500">Password</span>
            @if($passwordBaru)
                <span class="font-mono font-bold text-ink-900">{{ $passwordBaru }}</span>
            @elseif($passwordAwal)
                <span class="font-mono font-bold text-ink-900">{{ $passwordAwal }}</span>
            @else
                <span class="text-slate-500 text-right">Sudah diganti oleh pemilik akun<br>(tidak bisa dilihat)</span>
            @endif
        </div>
    </div>
    <p class="text-xs text-slate-400 mt-2">Password awal disimpan terenkripsi dan otomatis terhapus begitu pemilik akun menggantinya. Setiap pelihatan tercatat di log keamanan.</p>

    <form method="POST" action="{{ route('akun.reset', $akun) }}" class="mt-5" data-konfirmasi="Reset password akun ini? Password lama tidak berlaku lagi.">
        @csrf
        <button type="submit" class="tombol-aktif btn-sekunder"><x-ikon nama="key" class="w-4 h-4" /> Reset Password Baru</button>
    </form>
</div>
@endsection
