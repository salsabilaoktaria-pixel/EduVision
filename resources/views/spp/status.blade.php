@extends('layouts.app')
@section('title', 'Status SPP Saya')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-3 pr-4 font-semibold">Bulan/Tahun</th><th class="py-3 pr-4 font-semibold">Jumlah</th>
                <th class="py-3 pr-4 font-semibold">Status</th><th class="py-3 pr-4 font-semibold">Tanggal Bayar</th>
            </tr></thead>
            <tbody>
            @forelse($spp as $s)
                <tr class="border-b border-slate-50">
                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $s->bulan }} {{ $s->tahun }}</td>
                    <td class="py-3 pr-4">Rp {{ number_format($s->jumlah,0,',','.') }}</td>
                    <td class="py-3 pr-4">
                        @if($s->status=='Lunas')
                            <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Lunas</span>
                        @else
                            <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 text-slate-500">{{ $s->tanggal_bayar ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-10 text-slate-400">Belum ada tagihan SPP.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
