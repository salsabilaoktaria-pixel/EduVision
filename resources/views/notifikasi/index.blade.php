@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
        <p class="text-sm text-slate-500">Pemberitahuan terbaru untuk akunmu.</p>
        <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">
            @csrf
            <button class="text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl px-4 py-2 transition">Tandai semua dibaca</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
        @forelse($notifikasi as $n)
        <a href="{{ route('notifikasi.baca', $n) }}" class="flex gap-3 px-4 py-3.5 hover:bg-slate-50 transition {{ $n->dibaca_pada ? '' : 'bg-teal-50/60' }}">
            <span class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center {{ $n->dibaca_pada ? 'bg-slate-100 text-slate-500' : 'bg-teal-100 text-teal-700' }}">
                <x-ikon :nama="$n->namaIkon()" class="w-5 h-5" />
            </span>
            <span class="min-w-0 flex-1">
                <span class="block text-sm font-bold text-ink-900">{{ $n->judul }}</span>
                @if($n->isi)<span class="block text-xs text-slate-500 mt-0.5">{{ $n->isi }}</span>@endif
                <span class="block text-[11px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</span>
            </span>
            @unless($n->dibaca_pada)<span class="w-2 h-2 rounded-full bg-teal-500 mt-2 shrink-0"></span>@endunless
        </a>
        @empty
        <div class="flex items-start gap-4 px-2 py-8">
            <span class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0"><x-ikon nama="bell" class="w-6 h-6" /></span>
            <div>
                <p class="font-bold text-ink-900">Belum ada notifikasi</p>
                <p class="text-sm text-slate-400">Pemberitahuan baru akan muncul di sini.</p>
            </div>
        </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifikasi->links() }}</div>
</div>
@endsection
