@extends('layouts.app')
@section('title', 'Detail Guru')
@section('content')
<x-tombol-kembali :href="route('guru.index')" />

<div x-data="{ modalFoto: null }" @keydown.escape.window="modalFoto = null">
<div class="grid lg:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 lg:col-span-1 kartu">
        @if($guru->foto)
            <img src="{{ asset('storage/'.$guru->foto) }}" alt="Foto {{ $guru->nama }}" @click="modalFoto = '{{ asset('storage/'.$guru->foto) }}'"
                 class="w-28 h-28 rounded-2xl object-cover mb-4 ring-1 ring-slate-300 cursor-pointer hover:opacity-90 transition">
        @else
            <div class="mb-4"><x-avatar :nama="$guru->nama" class="w-28 h-28 !rounded-2xl" teks="text-4xl" /></div>
        @endif
        <h2 class="text-xl font-extrabold text-ink-900">{{ $guru->nama }}</h2>
        <p class="text-sm text-slate-500 mb-4">NIP: {{ $guru->nip }}</p>
        <div class="space-y-2 text-sm">
            <p><span class="text-slate-500">Jenis Kelamin:</span> <span class="font-semibold">{{ $guru->jenis_kelamin ?? '-' }}</span></p>
            <p><span class="text-slate-500">Tanggal Lahir:</span> <span class="font-semibold">{{ $guru->tanggal_lahir ?? '-' }}</span></p>
            <p><span class="text-slate-500">No. HP:</span> <span class="font-semibold">{{ $guru->no_hp ?? '-' }}</span></p>
            <p><span class="text-slate-500">Alamat:</span> <span class="font-semibold">{{ $guru->alamat ?? '-' }}</span></p>
        </div>
        @if(auth()->user()->isAdmin() && $guru->user)<a href="{{ route('akun.kredensial', $guru->user) }}" class="mt-5 tombol-aktif btn-sekunder w-full justify-center"><x-ikon nama="key" class="w-4 h-4" /> Lihat Username &amp; Password</a>@endif
        <div class="mt-5 flex justify-end"><x-aksi besar :edit="route('guru.edit', $guru)" :hapus="route('guru.destroy', $guru)" konfirmasi="Hapus data guru ini beserta akunnya?" /></div>
    </div>

    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
            <h3 class="font-bold text-ink-900 mb-3">Mata Pelajaran yang Diampu</h3>
            <div class="space-y-2">
                @forelse($guru->mataPelajaran as $m)
                    <div class="flex justify-between text-sm border-b border-slate-50 pb-2">
                        <span><x-ikon nama="book" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ $m->nama_mapel }} <span class="text-slate-400">({{ $m->kode_mapel }})</span></span>
                        <span class="text-slate-500">KKM {{ $m->kkm }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum mengampu mata pelajaran apapun.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
            <h3 class="font-bold text-ink-900 mb-3">Kelas yang Diwalikan</h3>
            <div class="space-y-2">
                @forelse($guru->kelasWali as $k)
                    <div class="flex justify-between text-sm border-b border-slate-50 pb-2">
                        <span><x-ikon nama="users" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ $k->nama_kelas }}</span>
                        <span class="text-slate-500">{{ $k->siswa->count() }} siswa</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum menjadi wali kelas manapun.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div x-show="modalFoto" x-cloak @click.self="modalFoto = null"
     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <img :src="modalFoto" class="w-full rounded-2xl shadow-2xl">
        <button @click="modalFoto = null" class="mt-3 mx-auto block bg-white/90 rounded-xl px-5 py-2 text-sm font-bold">Tutup</button>
    </div>
</div>
</div>
@endsection
