@extends('layouts.app')
@section('title', 'Tambah Materi Pembelajaran')
@section('content')
<x-tombol-kembali />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl" x-data="{ tipe: 'link_youtube' }">
    <form method="POST" action="{{ route('materi.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Untuk Kelas <span class="text-slate-400 font-normal">(kosongkan = semua kelas)</span></label>
                <select name="kelas_id" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)<option value="{{ $k->id }}" @selected(old('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Judul Materi</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Deskripsi <span class="text-slate-400 font-normal">(opsional)</span></label>
            <textarea name="deskripsi" rows="2" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('deskripsi') }}</textarea>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Tipe Materi</label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <label class="flex items-center justify-center gap-2 rounded-xl border-2 py-2.5 text-sm font-semibold cursor-pointer transition"
                       :class="tipe === 'link_youtube' ? 'bg-rose-600 text-white border-rose-600' : 'border-slate-300 text-slate-600'">
                    <input type="radio" name="tipe" value="link_youtube" x-model="tipe" class="hidden"> ▶️ Link YouTube
                </label>
                <label class="flex items-center justify-center gap-2 rounded-xl border-2 py-2.5 text-sm font-semibold cursor-pointer transition"
                       :class="tipe === 'file' ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-300 text-slate-600'">
                    <input type="radio" name="tipe" value="file" x-model="tipe" class="hidden"> 📄 Upload File
                </label>
            </div>
        </div>

        <div x-show="tipe === 'link_youtube'">
            <label class="text-sm font-semibold text-slate-700">Link YouTube</label>
            <input type="url" name="link" value="{{ old('link') }}" placeholder="https://youtube.com/watch?v=..." class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>
        <div x-show="tipe === 'file'" x-cloak>
            <label class="text-sm font-semibold text-slate-700">Upload File <span class="text-slate-400 font-normal">(PDF, Word, PPT, Excel, gambar — maks 20MB)</span></label>
            <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png" class="mt-1 w-full text-sm">
        </div>

        <div class="flex gap-3 pt-2">
            <button class="tombol-aktif bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition">Simpan</button>
            <a href="{{ route('materi.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
