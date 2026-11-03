@extends('layouts.app')
@section('title', 'Buat Tagihan SPP')
@section('content')
<x-tombol-kembali :href="route('spp.index')" />

<div x-data="{ nominalLain: false }" class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <form method="POST" action="{{ route('spp.store') }}" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Siswa</label>
            <select name="siswa_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $s)<option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</option>@endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Bulan</label>
                <select name="bulan" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Tahun</label><input type="text" name="tahun" value="{{ date('Y') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Nominal SPP</label>
            <p class="text-xs text-slate-400 mb-2">Setiap siswa bisa punya nominal berbeda sesuai kondisi ekonomi keluarga.</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach([50000, 100000, 150000, 200000] as $nominal)
                <label class="flex items-center gap-2 border-2 border-slate-300 rounded-xl px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                    <input type="radio" name="jumlah" value="{{ $nominal }}" @click="nominalLain=false" class="text-teal-600" {{ $loop->first ? 'checked' : '' }}>
                    Rp {{ number_format($nominal,0,',','.') }}
                </label>
                @endforeach
            </div>
            <label class="flex items-center gap-2 mt-2 text-sm">
                <input type="radio" name="jumlah" value="lain" @click="nominalLain=true" class="text-teal-600"> Nominal lain
            </label>
            <input type="number" x-show="nominalLain" x-cloak name="jumlah_custom" placeholder="Masukkan nominal custom" class="mt-2 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan Tagihan</button>
            <a href="{{ route('spp.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
