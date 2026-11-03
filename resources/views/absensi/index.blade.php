@extends('layouts.app')
@section('title', 'Rekap Absensi')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <select name="kelas_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
            </select>
        </form>
        <a href="{{ route('absensi.monitor') }}" class="bg-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-indigo-700 transition text-center tombol-aktif">🛰️ Monitor Foto & Lokasi</a>
    </div>

    {{-- Dipisah PER KELAS supaya tidak campur aduk semua siswa jadi satu daftar --}}
    @forelse($absensiPerKelas as $namaKelas => $daftar)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-teal-700 bg-teal-50 inline-block px-3 py-1 rounded-full mb-3">{{ $namaKelas }} <span class="opacity-60">({{ $daftar->count() }} absen)</span></h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                        <th class="py-3 pr-4 font-semibold">Foto</th><th class="py-3 pr-4 font-semibold">Siswa</th>
                        <th class="py-3 pr-4 font-semibold">Jam Masuk</th><th class="py-3 pr-4 font-semibold">Status</th>
                        <th class="py-3 pr-4 font-semibold">Verifikasi</th><th class="py-3 pr-4 font-semibold text-right">Aksi</th>
                    </tr></thead>
                    <tbody>
                    @foreach($daftar as $a)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                            <td class="py-3 pr-4">
                                @if($a->foto_wajah)
                                    <img src="{{ asset('storage/'.$a->foto_wajah) }}" class="w-10 h-10 rounded-lg object-cover">
                                @else <span class="text-slate-300">-</span> @endif
                            </td>
                            <td class="py-3 pr-4 font-semibold text-ink-900">
                                {{ $a->siswa->nama ?? '-' }}
                                @if($a->keterangan_detail)
                                    <p class="text-xs font-normal text-slate-400">"{{ $a->keterangan_detail }}"</p>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-slate-600">{{ \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') }} WIB</td>
                            <td class="py-3 pr-4">
                                @if($a->terverifikasi)
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold">{{ $a->status }} 🔒</span>
                                @else
                                <form method="POST" action="{{ route('absensi.status', $a) }}">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="rounded-lg border-2 border-slate-300 px-2 py-1 text-xs">
                                        @foreach(['Hadir','Sakit','Izin','Alpa'] as $st)
                                            <option value="{{ $st }}" @selected($a->status==$st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                @if($a->terverifikasi)
                                    <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Terverifikasi</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">Menunggu</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-right">
                                @if(!$a->terverifikasi)
                                <form method="POST" action="{{ route('absensi.verifikasi', $a) }}">
                                    @csrf
                                    <button class="px-3 py-1.5 rounded-lg bg-teal-100 text-teal-700 text-xs font-semibold hover:bg-teal-200">Verifikasi</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">Belum ada absensi di tanggal ini.</p>
    @endforelse
</div>
@endsection
