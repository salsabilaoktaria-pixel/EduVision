@extends('layouts.app')
@section('title', 'Kirim Aspirasi/Pengaduan')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <form method="POST" action="{{ route('aspirasi.store') }}" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Judul</label><input type="text" name="judul" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Isi Aspirasi / Pengaduan</label><textarea name="isi" rows="5" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none" placeholder="Ceritakan kejadian atau masukanmu secara jelas..."></textarea></div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="anonim" value="1" class="rounded border-slate-300 text-teal-600"> Kirim sebagai anonim
        </label>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kirim</button>
            <a href="{{ route('aspirasi.saya') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
