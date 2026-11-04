@extends('layouts.app')
@section('title', 'Detail Tagihan SPP')
@section('content')
<x-tombol-kembali :href="route('spp.index')" />

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-ink-900">{{ $spp->siswa->nama ?? '-' }}</h2>
                <p class="text-sm text-slate-500">Kelas {{ $spp->siswa->kelas->nama_kelas ?? '-' }} • NISN {{ $spp->siswa->nisn ?? '-' }}</p>
            </div>
            @if($spp->status === 'Lunas')
                <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold">Lunas</span>
            @else
                <span class="px-3 py-1.5 rounded-full bg-rose-100 text-rose-700 text-sm font-bold">Belum Lunas</span>
            @endif
        </div>

        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-slate-500">Periode</dt><dd class="font-bold text-ink-900 text-base mt-0.5">{{ $spp->bulan }} {{ $spp->tahun }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-slate-500">Nominal</dt><dd class="font-bold text-ink-900 text-base mt-0.5">Rp {{ number_format($spp->jumlah, 0, ',', '.') }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-slate-500">Tanggal Bayar</dt><dd class="font-bold text-ink-900 text-base mt-0.5">{{ $spp->tanggal_bayar ? \Carbon\Carbon::parse($spp->tanggal_bayar)->translatedFormat('d F Y') : 'Belum dibayar' }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-slate-500">Dibuat</dt><dd class="font-bold text-ink-900 text-base mt-0.5">{{ $spp->created_at ? $spp->created_at->translatedFormat('d F Y') : '-' }}</dd></div>
        </dl>

        {{-- Tombol aksi seragam: Detail (halaman ini), Edit, Hapus — plus Tandai Lunas jika belum lunas --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mt-6">
            @if($spp->status !== 'Lunas')
            <form method="POST" action="{{ route('spp.bayar', $spp) }}">
                @csrf
                <button class="btn-aksi" style="background:#e4efdf;border-color:#a8c99c;color:#2f4a2d"><x-ikon nama="check-circle" class="w-3.5 h-3.5" />Tandai Lunas</button>
            </form>
            @else<span></span>@endif
            <x-aksi besar :edit="route('spp.edit', $spp)" :hapus="route('spp.destroy', $spp)" konfirmasi="Hapus tagihan ini?" />
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
        <h3 class="font-bold text-ink-900 mb-3">Tagihan lain siswa ini</h3>
        <div class="space-y-2">
            @forelse($tagihanLain as $t)
                <a href="{{ route('spp.show', $t) }}" class="flex items-center justify-between text-sm rounded-xl border-2 border-slate-100 px-3 py-2 hover:border-teal-300 transition" data-popup>
                    <span class="font-semibold text-ink-900">{{ $t->bulan }} {{ $t->tahun }}</span>
                    <span class="text-xs font-bold {{ $t->status === 'Lunas' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $t->status }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400">Belum ada tagihan lain.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
