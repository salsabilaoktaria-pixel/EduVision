@extends('layouts.app')
@section('title', 'Tambah Soal Manual')
@section('content')
<x-tombol-kembali :href="route('banksoal.index')" />

<!-- Navigasi antar metode tambah soal (halaman terpisah, bukan tab, supaya tidak reset saat error) -->
<div class="flex gap-2 mb-5 flex-wrap">
    <span class="px-4 py-2.5 text-sm font-bold rounded-xl bg-teal-600 text-white">✍️ Buat Manual</span>
    <a href="{{ route('banksoal.importForm') }}" class="px-4 py-2.5 text-sm font-bold rounded-xl bg-white border-2 border-slate-300 text-slate-600 hover:bg-slate-50">📁 Import File</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <form method="POST" action="{{ route('banksoal.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Pertanyaan</label><textarea name="pertanyaan" rows="2" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('pertanyaan') }}</textarea></div>
        <div><label class="text-sm font-semibold text-slate-700">Gambar Soal <span class="text-slate-400 font-normal">(opsional, mis. grafik/diagram/peta)</span></label><input type="file" name="gambar" accept="image/*" class="mt-1 w-full text-sm"></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Pilihan A</label><input type="text" name="pilihan_a" value="{{ old('pilihan_a') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Pilihan B</label><input type="text" name="pilihan_b" value="{{ old('pilihan_b') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Pilihan C</label><input type="text" name="pilihan_c" value="{{ old('pilihan_c') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Pilihan D</label><input type="text" name="pilihan_d" value="{{ old('pilihan_d') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Pilihan E <span class="text-slate-400 font-normal">(opsional)</span></label><input type="text" name="pilihan_e" value="{{ old('pilihan_e') }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Jawaban Benar</label>
                <select name="jawaban_benar" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['A','B','C','D','E'] as $h)<option value="{{ $h }}" @selected(old('jawaban_benar')==$h)>{{ $h }}</option>@endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Tingkat Kesulitan</label>
                <select name="tingkat_kesulitan" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Mudah','Sedang','Sulit'] as $t)<option value="{{ $t }}" @selected(old('tingkat_kesulitan')==$t)>{{ $t }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan</button>
            <a href="{{ route('banksoal.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
