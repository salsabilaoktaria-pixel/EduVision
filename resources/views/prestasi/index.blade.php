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
            <button class="bg-teal-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-teal-700 tombol-aktif"><x-ikon nama="search" class="w-4 h-4" /> Cari</button>
            @if(request('cari'))
                <a href="{{ route('prestasi.index') }}" class="bg-slate-100 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200 flex items-center btn-sekunder"><x-ikon nama="x" class="w-4 h-4" /> Reset</a>
            @endif
        </form>
        <a href="{{ route('prestasi.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Prestasi</a>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-5 text-center">
        <div class="rounded-xl border-2 border-slate-200 bg-teal-50 p-3"><p class="text-2xl font-extrabold text-ink-900">{{ $ringkas['semester'] }}</p><p class="text-[11px] font-bold text-slate-500 uppercase">Prestasi semester ini</p></div>
        <div class="rounded-xl border-2 border-slate-200 bg-teal-50 p-3"><p class="text-2xl font-extrabold text-ink-900">{{ $ringkas['siswa'] }}</p><p class="text-[11px] font-bold text-slate-500 uppercase">Siswa berprestasi</p></div>
        <div class="rounded-xl border-2 border-slate-200 bg-teal-50 p-3"><p class="text-2xl font-extrabold text-ink-900">{{ $ringkas['total'] }}</p><p class="text-[11px] font-bold text-slate-500 uppercase">Total semua waktu</p></div>
    </div>

    {{-- Dikelompokkan per KELAS sama seperti halaman SPP: tiap kelas bisa
         dibuka/tutup (accordion), dan semua siswa di kelas itu tampil
         (termasuk yang belum punya prestasi) supaya mudah dipantau per kelas. --}}
    @forelse($siswaPerKelas as $namaKelas => $daftarSiswa)
        <div class="mb-6" x-data="{ buka: true }">
            <button type="button" @click="buka = !buka" class="w-full flex items-center justify-between gap-3 bg-slate-50 hover:bg-slate-100 rounded-xl px-4 py-3 mb-2 transition">
                <span class="font-bold text-ink-900 text-sm"><x-ikon nama="users" class="w-4 h-4 inline-block align-[-2px] mr-1" /> Kelas {{ $namaKelas }} <span class="text-slate-400 font-normal">({{ $daftarSiswa->count() }} siswa)</span></span>
                <span class="text-slate-400 text-xs inline-flex items-center gap-1"><span x-text="buka ? 'Tutup' : 'Buka'"></span><x-ikon nama="chevron-down" class="w-4 h-4 transition" x-bind:class="buka ? 'rotate-180' : ''" /></span>
            </button>
            <div x-show="buka" class="overflow-x-auto">
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
                    @foreach($daftarSiswa as $siswaSatu)
                        @php $daftarPrestasiSiswa = $prestasiPerSiswa->get($siswaSatu->id, collect()); @endphp
                        @if($daftarPrestasiSiswa->isEmpty())
                            <tr class="border-b border-slate-50">
                                <td class="py-3 pr-4 text-slate-300 text-xs">-</td>
                                <td class="py-3 pr-4 font-semibold text-ink-900"><span class="flex items-center gap-2.5"><x-avatar :nama="$siswaSatu->nama" :foto="$siswaSatu->foto" class="w-8 h-8" teks="text-xs" />{{ $siswaSatu->nama }}@if(($totalPerSiswa[$siswaSatu->id] ?? 0) > 0) <span class="ml-1 text-[10px] font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full px-1.5 py-0.5 whitespace-nowrap">{{ $totalPerSiswa[$siswaSatu->id] }} prestasi</span>@endif</span></td>
                                <td class="py-3 pr-4 text-slate-400 italic" colspan="4">Belum ada prestasi tercatat.</td>
                            </tr>
                        @else
                            @foreach($daftarPrestasiSiswa as $p)
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
                                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $siswaSatu->nama }}@if(($totalPerSiswa[$siswaSatu->id] ?? 0) > 0) <span class="ml-1 text-[10px] font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full px-1.5 py-0.5 whitespace-nowrap">{{ $totalPerSiswa[$siswaSatu->id] }} prestasi</span>@endif</td>
                                    <td class="py-3 pr-4 text-slate-600"><x-ikon nama="award" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ $p->nama_prestasi }} @if($p->peringkat) <span class="text-slate-400">({{ $p->peringkat }})</span> @endif</td>
                                    <td class="py-3 pr-4 text-slate-600">{{ $p->tingkat }}</td>
                                    <td class="py-3 pr-4 text-slate-600">{{ $p->tanggal }}</td>
                                    <td class="py-3 pr-4">
                                        <div class="flex justify-end"><x-aksi :detail="route('prestasi.show', $p)" :edit="route('prestasi.edit', $p)" :hapus="route('prestasi.destroy', $p)" konfirmasi="Hapus prestasi ini?" /></div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">
            @if(request('cari'))
                Tidak ditemukan prestasi untuk pencarian "{{ request('cari') }}".
            @else
                Belum ada data siswa.
            @endif
        </p>
    @endforelse
</div>

<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5 mt-5" x-data="{ buka: true }">
    <button type="button" @click="buka = !buka" class="w-full flex items-center justify-between gap-3">
        <h3 class="font-display text-lg text-ink-900"><x-ikon nama="award" class="w-5 h-5 inline-block align-[-3px] mr-1" /> Rekap Prestasi per Siswa <span class="text-xs font-normal text-slate-500">(semua waktu)</span></h3>
        <x-ikon nama="chevron-down" class="w-4 h-4" />
    </button>
    <div x-show="buka" class="overflow-x-auto mt-3">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100"><th class="py-2 pr-4 font-semibold">Siswa</th><th class="py-2 pr-4 font-semibold">Kelas</th><th class="py-2 pr-4 font-semibold text-center">Jumlah</th><th class="py-2 pr-4 font-semibold">Daftar Prestasi</th></tr></thead>
            <tbody>
            @forelse($rekap as $r)
                <tr class="border-b border-slate-50 align-top">
                    <td class="py-3 pr-4 font-semibold text-ink-900"><span class="flex items-center gap-2.5"><x-avatar :nama="$r['siswa']->nama" :foto="$r['siswa']->foto" class="w-8 h-8" teks="text-xs" />{{ $r['siswa']->nama }}</span></td>
                    <td class="py-3 pr-4 text-slate-600">{{ $r['siswa']->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-3 pr-4 text-center"><span class="inline-block min-w-[1.75rem] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 text-xs font-extrabold">{{ $r['jumlah'] }}</span></td>
                    <td class="py-3 pr-4 text-slate-600">
                        @foreach($r['daftar'] as $pp)
                            <a href="{{ route('prestasi.show', $pp) }}" class="block hover:text-teal-700">• {{ $pp->nama_prestasi }} <span class="text-slate-400">— {{ $pp->peringkat ?: $pp->tingkat }}, {{ \Carbon\Carbon::parse($pp->tanggal)->translatedFormat('d M Y') }}</span></a>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-6 text-slate-400">Belum ada prestasi tercatat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
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
