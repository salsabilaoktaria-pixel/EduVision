@extends('layouts.app')
@section('title', 'Kotak Aspirasi & Pengaduan')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="space-y-4">
@forelse($aspirasi as $a)
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-ink-900">{{ $a->judul }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Oleh: {{ $a->anonim ? 'Anonim' : ($a->user->name ?? '-') }} • {{ $a->created_at->diffForHumans() }}</p>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-semibold
                @class(['bg-amber-100 text-amber-700' => $a->status=='Baru', 'bg-blue-100 text-blue-700' => $a->status=='Diproses', 'bg-emerald-100 text-emerald-700' => $a->status=='Selesai'])">
                {{ $a->status }}
            </span>
        </div>
        <p class="text-sm text-slate-600 mt-3">{{ $a->isi }}</p>

        @if($a->tanggapan)
            <div class="mt-3 bg-slate-50 rounded-xl p-3 text-sm text-slate-600"><b>Tanggapan:</b> {{ $a->tanggapan }}</div>
        @endif

        <form method="POST" action="{{ route('aspirasi.tanggapi', $a) }}" class="mt-3 flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="text" name="tanggapan" placeholder="Tulis tanggapan..." value="{{ $a->tanggapan }}" class="flex-1 rounded-xl border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <select name="status" class="rounded-xl border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="Baru" @selected($a->status=='Baru')>Baru</option>
                <option value="Diproses" @selected($a->status=='Diproses')>Diproses</option>
                <option value="Selesai" @selected($a->status=='Selesai')>Selesai</option>
            </select>
            <button class="bg-teal-600 text-white rounded-xl px-4 py-2 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kirim</button>
        </form>
    </div>
@empty
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-10 text-center text-slate-400 kartu">Belum ada aspirasi masuk.</div>
@endforelse
</div>
<div class="mt-4">{{ $aspirasi->links() }}</div>
@endsection
