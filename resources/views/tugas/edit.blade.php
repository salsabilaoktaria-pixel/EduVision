@extends('layouts.app')
@section('title', 'Edit Tugas')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <form method="POST" action="{{ route('tugas.update', $tugas) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Kelas</label>
                <select name="kelas_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)<option value="{{ $k->id }}" @selected(old('kelas_id', $tugas->kelas_id)==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
                </select>
                @error('kelas_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id', $tugas->mata_pelajaran_id)==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
                </select>
                @error('mata_pelajaran_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Semester</label>
                <select name="semester" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="Ganjil" @selected(old('semester', $tugas->semester)=='Ganjil')>Ganjil</option>
                    <option value="Genap" @selected(old('semester', $tugas->semester)=='Genap')>Genap</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tugas->tahun_ajaran) }}" placeholder="2025/2026" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('tahun_ajaran')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <p class="text-xs text-slate-400 -mt-2">Semester & tahun ajaran dipakai supaya nilai tugas ini bisa otomatis nyambung ke Input Nilai siswa.</p>

        <div>
            <label class="text-sm font-semibold text-slate-700">Judul Tugas</label>
            <input type="text" name="judul" value="{{ old('judul', $tugas->judul) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('judul')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Deskripsi / Instruksi</label>
            <textarea name="deskripsi" rows="4" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Deadline <span class="text-rose-500">*</span></label>
            <input type="datetime-local" name="deadline" value="{{ old('deadline', $tugas->deadline->format('Y-m-d\TH:i')) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('deadline')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="border-t border-slate-100 pt-4">
            @if($tugas->lampiran->isNotEmpty())
                <label class="text-sm font-semibold text-slate-700">Lampiran Saat Ini</label>
                <div class="mt-1 space-y-1.5">
                    @foreach($tugas->lampiran as $l)
                        <div class="flex items-center justify-between gap-2 bg-slate-50 rounded-lg px-3 py-2">
                            @if($l->tipe === 'file')
                                <a href="{{ asset('storage/'.$l->file_path) }}" target="_blank" class="text-xs font-semibold text-teal-600 hover:underline truncate">⬇️ {{ $l->file_nama_asli }}</a>
                            @else
                                <a href="{{ $l->link }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline truncate">🔗 {{ $l->link }}</a>
                            @endif
                            <form method="POST" action="{{ route('tugas.lampiran.hapus', $l) }}" onsubmit="return confirm('Hapus lampiran ini?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-rose-500 hover:underline shrink-0">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <label class="text-sm font-semibold text-slate-700 mt-3 block">Tambah Lampiran (opsional)</label>
            <p class="text-xs text-slate-400 mb-2">Boleh beberapa file sekaligus: .docx, .pdf, gambar, .txt, atau video.</p>
            <input type="file" name="lampiran_file[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,.mp4,.mov,.avi,.webm"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('lampiran_file.*')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror

            <label class="text-sm font-semibold text-slate-700 mt-3 block">Tambah Link (opsional)</label>
            <textarea name="lampiran_link" rows="2" placeholder="https://..." class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan Perubahan</button>
            <a href="{{ route('tugas.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
