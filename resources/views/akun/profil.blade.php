@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<x-tombol-kembali :first-page="true">
    <a href="{{ route('akun.gantiPassword') }}" class="tombol-aktif btn-sekunder"><x-ikon nama="lock" class="w-4 h-4" /> Ganti Password</a>
</x-tombol-kembali>

<div class="grid lg:grid-cols-3 gap-5">
    <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6 text-center h-fit">
        <div class="flex justify-center mb-4">
            @if($user->fotoUrl())
                <img src="{{ $user->fotoUrl() }}" alt="Foto {{ $user->name }}" @click="fotoModalUrl = '{{ $user->fotoUrl() }}'"
                     class="w-32 h-32 rounded-full object-cover ring-2 ring-teal-200 cursor-zoom-in">
            @else
                <x-avatar :nama="$user->name" class="w-32 h-32" teks="text-4xl" />
            @endif
        </div>
        <h2 class="text-xl font-extrabold text-ink-900 break-words">{{ $user->name }}</h2>
        <p class="mt-2 inline-flex items-center rounded-full border-2 border-teal-300 bg-teal-50 px-4 py-1 text-xs font-bold text-teal-800">{{ $akun['Peran'] }}</p>
        <p class="text-xs text-slate-500 mt-3 break-all">{{ $user->email }}</p>
        <p class="text-[11px] text-slate-400 mt-1">Akun dibuat {{ $terdaftar }}</p>
        @if($ringkas)
        <div class="mt-5 grid gap-2 text-left">
            @foreach($ringkas as $label => $nilai)
                <div class="rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-2.5">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="text-lg font-extrabold text-ink-900">{{ $nilai }}</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="lg:col-span-2 space-y-5">
        <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6">
            <h3 class="font-bold text-ink-900 mb-4">Data Akun</h3>
            <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                @foreach($akun as $k => $v)
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $k }}</dt><dd class="font-semibold text-ink-900 break-words">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </div>
        @if($biodata)
        <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6">
            <h3 class="font-bold text-ink-900 mb-4">Biodata Lengkap</h3>
            <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                @foreach($biodata as $k => $v)
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $k }}</dt><dd class="font-semibold text-ink-900 break-words">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </div>
        @endif
        <p class="text-xs text-slate-500">Untuk mengubah biodata, hubungi admin sekolah. Password dapat kamu ganti sendiri lewat tombol Ganti Password.</p>
    </div>
</div>
@endsection
