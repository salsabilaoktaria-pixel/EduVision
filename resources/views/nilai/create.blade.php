@extends('layouts.app')
@section('title', 'Tambah Nilai')
@section('content')
<x-tombol-kembali :href="route('nilai.index')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-5 text-xs text-indigo-700">
        <p class="font-bold mb-1">Alur pengisian nilai:</p>
        <p>1. Isi data dasar di bawah ini (siswa, mapel, semester) →
           2. Input semua nilai <b>tugas harian</b> satu per satu →
           3. Sistem hitung rata-ratanya jadi Nilai Tugas →
           4. Baru isi Nilai UTS & UAS →
           5. Nilai Akhir otomatis dihitung.</p>
    </div>

    <form method="POST" action="{{ route('nilai.store') }}" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Siswa</label>
            <select name="siswa_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $namaKelas => $daftarSiswa)
                    <optgroup label="{{ $namaKelas }}">
                        @foreach($daftarSiswa as $s)<option value="{{ $s->id }}">{{ $s->nama }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Semester</label>
                <select name="semester" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="Ganjil">Ganjil</option><option value="Genap">Genap</option>
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Tahun Ajaran</label><input type="text" name="tahun_ajaran" placeholder="2025/2026" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan & Lanjut Isi Tugas Harian →</button>
            <a href="{{ route('nilai.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
