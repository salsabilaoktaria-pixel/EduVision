@extends('layouts.app')
@section('title', 'Tambah Prestasi')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <form method="POST" action="{{ route('prestasi.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Siswa</label>
            <select name="siswa_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $s)<option value="{{ $s->id }}">{{ $s->nama }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Nama Prestasi</label><input type="text" name="nama_prestasi" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Tingkat</label>
                <select name="tingkat" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option>Sekolah</option><option>Kecamatan</option><option>Kota/Kabupaten</option><option>Provinsi</option><option>Nasional</option><option>Internasional</option>
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Peringkat</label><input type="text" name="peringkat" placeholder="Juara 1, dst" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Tanggal</label><input type="date" name="tanggal" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">File Bukti (opsional)</label><input type="file" name="file_bukti" class="mt-1 w-full text-sm"></div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan</button>
            <a href="{{ route('prestasi.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
