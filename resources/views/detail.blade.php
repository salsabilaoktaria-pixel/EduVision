@extends('layouts.app')
@section('title', $judul)
@section('content')
{{-- Halaman Detail generik (dipakai Jadwal, Bank Soal, Nilai, Materi): daftar "label : isi" + tombol aksi baku. --}}
<x-tombol-kembali :href="$kembali" />
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-6 max-w-3xl">
    <dl class="divide-y divide-slate-100">
        @foreach($baris as $label => $isi)
            <div class="py-3 grid sm:grid-cols-3 gap-1 sm:gap-4">
                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500 pt-0.5">{{ $label }}</dt>
                <dd class="sm:col-span-2 text-sm font-semibold text-ink-900 whitespace-pre-line">{{ $isi !== null && $isi !== '' ? $isi : '-' }}</dd>
            </div>
        @endforeach
    </dl>
    @if(!empty($gambar))
        <a href="{{ $gambar }}" target="_blank" class="block mt-4"><img src="{{ $gambar }}" alt="Sertifikat" class="w-full rounded-xl border-2 border-slate-300"></a>
    @endif
    <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
            @foreach(($tambahan ?? []) as $t)
                <a href="{{ $t['url'] }}" @if(!empty($t['baru'])) target="_blank" @endif class="btn-aksi btn-detail"><x-ikon :nama="$t['ikon'] ?? 'arrow-right'" class="w-3.5 h-3.5" />{{ $t['label'] }}</a>
            @endforeach
        </div>
        <x-aksi besar :edit="$edit ?? null" :hapus="$hapus ?? null" />
    </div>
</div>
@endsection
