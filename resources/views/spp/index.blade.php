@extends('layouts.app')
@section('title', 'Data SPP')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Status --</option>
                <option value="Lunas" @selected(request('status')=='Lunas')>Lunas</option>
                <option value="Belum Lunas" @selected(request('status')=='Belum Lunas')>Belum Lunas</option>
            </select>
        </form>
        <a href="{{ route('spp.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif">+ Buat Tagihan</a>
    </div>

    {{-- Dikelompokkan per KELAS, dan di dalamnya tiap SISWA tampil satu per satu
         (termasuk yang belum pernah dibuatkan tagihan), supaya admin bisa lihat
         seisi satu kelas sekaligus tanpa harus menyisir baris tagihan satu-satu. --}}
    @forelse($siswaPerKelas as $namaKelas => $daftarSiswa)
        <div class="mb-6" x-data="{ buka: true }">
            <button type="button" @click="buka = !buka" class="w-full flex items-center justify-between gap-3 bg-slate-50 hover:bg-slate-100 rounded-xl px-4 py-3 mb-2 transition">
                <span class="font-bold text-ink-900 text-sm">🏫 Kelas {{ $namaKelas }} <span class="text-slate-400 font-normal">({{ $daftarSiswa->count() }} siswa)</span></span>
                <span class="text-slate-400 text-xs" x-text="buka ? '▲ Tutup' : '▼ Buka'"></span>
            </button>
            <div x-show="buka" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                        <th class="py-3 pr-4 font-semibold">Siswa</th><th class="py-3 pr-4 font-semibold">Bulan/Tahun</th>
                        <th class="py-3 pr-4 font-semibold">Jumlah</th><th class="py-3 pr-4 font-semibold">Status</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
                    </tr></thead>
                    <tbody>
                    @foreach($daftarSiswa as $siswaSatu)
                        @php $tagihan = $sppPerSiswa->get($siswaSatu->id, collect()); @endphp
                        @if($tagihan->isEmpty())
                            @unless(request('status'))
                                <tr class="border-b border-slate-50">
                                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $siswaSatu->nama }}</td>
                                    <td class="py-3 pr-4 text-slate-400 italic" colspan="4">Belum ada tagihan SPP.</td>
                                </tr>
                            @endunless
                        @else
                            @foreach($tagihan as $s)
                                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                                    <td class="py-3 pr-4 font-semibold text-ink-900">{{ $siswaSatu->nama }}</td>
                                    <td class="py-3 pr-4 text-slate-600">{{ $s->bulan }} {{ $s->tahun }}</td>
                                    <td class="py-3 pr-4 text-slate-600">Rp {{ number_format($s->jumlah,0,',','.') }}</td>
                                    <td class="py-3 pr-4">
                                        @if($s->status=='Lunas')
                                            <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Lunas</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div class="flex justify-end gap-2">
                                            @if($s->status != 'Lunas')
                                            <form method="POST" action="{{ route('spp.bayar', $s) }}">
                                                @csrf
                                                <button class="px-3 py-1.5 rounded-lg bg-teal-100 text-teal-700 text-xs font-semibold hover:bg-teal-200">Tandai Lunas</button>
                                            </form>
                                            @endif
                                            <form method="POST" action="{{ route('spp.destroy', $s) }}" onsubmit="return confirm('Hapus tagihan ini?')">
                                                @csrf @method('DELETE')
                                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                                            </form>
                                        </div>
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
        <p class="text-center py-10 text-slate-400">Belum ada data siswa.</p>
    @endforelse
</div>
@endsection
