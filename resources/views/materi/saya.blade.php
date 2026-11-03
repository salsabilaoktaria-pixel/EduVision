@extends('layouts.app')
@section('title', 'Materi Pembelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <form method="GET" class="mb-5">
        <select name="mapel_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <option value="">-- Semua Mapel --</option>
            @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(request('mapel_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
        </select>
    </form>

    @forelse($materi as $namaMapel => $daftar)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-teal-700 bg-teal-50 inline-block px-3 py-1 rounded-full mb-3">{{ $namaMapel }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($daftar as $m)
                    <div class="kartu border-2 border-slate-200 rounded-xl p-4">
                        <p class="font-bold text-ink-900">{{ $m->judul }}</p>
                        @if($m->deskripsi)<p class="text-xs text-slate-500 mt-1">{{ $m->deskripsi }}</p>@endif
                        <p class="text-xs text-slate-400 mt-1 mb-2">Oleh {{ $m->guru->nama ?? '-' }} • {{ $m->created_at->translatedFormat('d M Y') }}</p>

                        @if($m->tipe === 'link_youtube' && $m->linkEmbedYoutube())
                            <div class="aspect-video rounded-xl overflow-hidden border-2 border-slate-200">
                                <iframe src="{{ $m->linkEmbedYoutube() }}" class="w-full h-full" allowfullscreen></iframe>
                            </div>
                        @elseif($m->tipe === 'link_youtube')
                            <a href="{{ $m->link }}" target="_blank" class="tombol-aktif block text-center bg-rose-600 text-white rounded-lg px-3 py-2 text-xs font-bold hover:bg-rose-700 transition">▶️ Buka Video</a>
                        @else
                            <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank" class="tombol-aktif block text-center bg-indigo-600 text-white rounded-lg px-3 py-2 text-xs font-bold hover:bg-indigo-700 transition">
                                ⬇️ Unduh {{ strtoupper($m->ekstensiFile()) }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center py-12 text-slate-400">Belum ada materi untuk kelasmu.</p>
    @endforelse
</div>
@endsection
