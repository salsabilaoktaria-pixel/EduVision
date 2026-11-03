@extends('layouts.app')
@section('title', 'Materi Pembelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <select name="mapel_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(request('mapel_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </form>
        @unless(auth()->user()->isAdmin())
        <a href="{{ route('materi.create') }}" class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition text-center">+ Tambah Materi</a>
        @endunless
    </div>

    @forelse($materi as $namaMapel => $daftar)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-teal-700 bg-teal-50 inline-block px-3 py-1 rounded-full mb-3">{{ $namaMapel }}</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($daftar as $m)
                    <div class="kartu border-2 border-slate-200 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg shrink-0
                                {{ $m->tipe === 'link_youtube' ? 'bg-rose-100 text-rose-700' : (in_array($m->ekstensiFile(),['pdf']) ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700') }}">
                                {{ $m->tipe === 'link_youtube' ? '▶️' : ($m->ekstensiFile() === 'pdf' ? '📕' : '📄') }}
                            </div>
                            @unless(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('materi.destroy', $m) }}" onsubmit="return confirm('Hapus materi ini?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-rose-500 hover:text-rose-700 font-semibold">Hapus</button>
                            </form>
                            @endunless
                        </div>
                        <p class="font-bold text-ink-900 mt-2">{{ $m->judul }}</p>
                        @if($m->deskripsi)<p class="text-xs text-slate-500 mt-1">{{ $m->deskripsi }}</p>@endif
                        <p class="text-xs text-slate-400 mt-2">{{ $m->kelas->nama_kelas ?? 'Semua kelas' }} • {{ $m->created_at->translatedFormat('d M Y') }}</p>
                        @if($m->tipe === 'link_youtube')
                            <a href="{{ $m->link }}" target="_blank" class="tombol-aktif mt-3 block text-center bg-rose-600 text-white rounded-lg px-3 py-2 text-xs font-bold hover:bg-rose-700 transition">▶️ Buka Video</a>
                        @else
                            <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank" class="tombol-aktif mt-3 block text-center bg-indigo-600 text-white rounded-lg px-3 py-2 text-xs font-bold hover:bg-indigo-700 transition">⬇️ Unduh File</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center py-12 text-slate-400">Belum ada materi yang diunggah.</p>
    @endforelse
</div>
@endsection
