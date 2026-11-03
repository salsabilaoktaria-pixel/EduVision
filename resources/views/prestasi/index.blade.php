@extends('layouts.app')
@section('title', 'Prestasi Siswa')
@section('content')
<x-tombol-kembali :first-page="true" />

<div x-data="{ modalFoto: null }" @keydown.escape.window="modalFoto = null">

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" id="formCariPrestasi" class="flex gap-2">
            <input type="text" name="cari" id="inputCariPrestasi" value="{{ request('cari') }}"
                placeholder="Cari nama siswa / nama prestasi..."
                class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-teal-500 outline-none">
            <button class="bg-teal-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-teal-700 tombol-aktif">Cari</button>
            @if(request('cari'))
                <a href="{{ route('prestasi.index') }}" class="bg-slate-100 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200 flex items-center">Reset</a>
            @endif
        </form>
        <a href="{{ route('prestasi.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif">+ Tambah Prestasi</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-3 pr-4 font-semibold">Bukti</th>
                <th class="py-3 pr-4 font-semibold">Siswa</th>
                <th class="py-3 pr-4 font-semibold">Prestasi</th>
                <th class="py-3 pr-4 font-semibold">Tingkat</th>
                <th class="py-3 pr-4 font-semibold">Tanggal</th>
                <th class="py-3 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($prestasi as $p)
                @php
                    $isGambar = $p->file_bukti && preg_match('/\.(jpg|jpeg|png)$/i', $p->file_bukti);
                    $isPdf = $p->file_bukti && preg_match('/\.pdf$/i', $p->file_bukti);
                @endphp
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-3 pr-4">
                        @if($isGambar)
                            <img src="{{ asset('storage/'.$p->file_bukti) }}"
                                 @click="modalFoto = '{{ asset('storage/'.$p->file_bukti) }}'"
                                 class="w-12 h-12 rounded-lg object-cover cursor-pointer ring-1 ring-slate-200 hover:ring-teal-400 transition">
                        @elseif($isPdf)
                            <a href="{{ asset('storage/'.$p->file_bukti) }}" target="_blank"
                               class="w-12 h-12 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">PDF</a>
                        @else
                            <span class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300 text-xs">-</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $p->siswa->nama ?? '-' }}</td>
                    <td class="py-3 pr-4 text-slate-600">🏆 {{ $p->nama_prestasi }} @if($p->peringkat) <span class="text-slate-400">({{ $p->peringkat }})</span> @endif</td>
                    <td class="py-3 pr-4 text-slate-600">{{ $p->tingkat }}</td>
                    <td class="py-3 pr-4 text-slate-600">{{ $p->tanggal }}</td>
                    <td class="py-3 pr-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('prestasi.edit', $p) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                            <form method="POST" action="{{ route('prestasi.destroy', $p) }}" onsubmit="return confirm('Hapus prestasi ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-10 text-slate-400">
                    @if(request('cari'))
                        Tidak ditemukan prestasi untuk pencarian "{{ request('cari') }}".
                    @else
                        Belum ada data prestasi.
                    @endif
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $prestasi->links() }}</div>
</div>

<!-- Modal Popup Foto Bukti -->
<div x-show="modalFoto" x-cloak @click.self="modalFoto = null"
     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <img :src="modalFoto" class="w-full rounded-2xl shadow-2xl">
        <button @click="modalFoto = null" class="mt-3 mx-auto block bg-white/90 rounded-xl px-5 py-2 text-sm font-bold">Tutup</button>
    </div>
</div>
</div>
@endsection
