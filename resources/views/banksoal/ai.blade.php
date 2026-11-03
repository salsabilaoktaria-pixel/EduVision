@extends('layouts.app')
@section('title', 'Buat Soal dengan AI')
@section('content')
<x-tombol-kembali :href="route('banksoal.index')" />

<div class="flex gap-2 mb-5 flex-wrap">
    <a href="{{ route('banksoal.create') }}" class="px-4 py-2.5 text-sm font-bold rounded-xl bg-white border-2 border-slate-300 text-slate-600 hover:bg-slate-50">✍️ Buat Manual</a>
    <a href="{{ route('banksoal.importForm') }}" class="px-4 py-2.5 text-sm font-bold rounded-xl bg-white border-2 border-slate-300 text-slate-600 hover:bg-slate-50">📁 Import File</a>
    <span class="px-4 py-2.5 text-sm font-bold rounded-xl bg-teal-600 text-white">✨ Buat dengan AI</span>
</div>

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-4 text-xs text-amber-700">
        Fitur ini memanggil AI (Google Gemini) untuk meracik soal otomatis. Butuh <b>GEMINI_API_KEY</b> aktif di file <code>.env</code> — lihat TUTORIAL.md bagian 14.4. Proses bisa memakan waktu beberapa detik, mohon tunggu setelah klik tombol.
    </div>
    <form method="POST" action="{{ route('banksoal.generateAi') }}" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Tingkat Kelas</label><input type="text" name="tingkat_kelas" value="{{ old('tingkat_kelas') }}" placeholder="Contoh: X SMA" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Topik / Materi</label><input type="text" name="topik" value="{{ old('topik') }}" placeholder="Contoh: Sistem Persamaan Linear" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Jumlah Soal</label><input type="number" name="jumlah_soal" value="{{ old('jumlah_soal', 5) }}" min="1" max="20" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Tingkat Kesulitan</label>
                <select name="tingkat_kesulitan" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Mudah','Sedang','Sulit'] as $t)<option value="{{ $t }}" @selected(old('tingkat_kesulitan', 'Sedang')==$t)>{{ $t }}</option>@endforeach
                </select>
            </div>
        </div>
        <button type="submit" id="btnGenerateAi" onclick="document.getElementById('loadingAi').classList.remove('hidden'); this.disabled=true; this.innerText='Sedang membuat soal...';" class="w-full bg-gradient-to-r from-violet-500 to-indigo-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:opacity-90 transition">✨ Buatkan Soal dengan AI</button>
        <p id="loadingAi" class="hidden text-xs text-slate-400 text-center">AI sedang meracik soal, mohon tunggu (biasanya 5–20 detik)...</p>
    </form>
</div>
@endsection
