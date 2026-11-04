@extends('layouts.app')
@section('title', 'Persetujuan Absen')
@section('content')
<x-tombol-kembali :first-page="true" />

<div x-data="{ modalFoto: null }" @keydown.escape.window="modalFoto = null">

<div class="mb-5 rounded-2xl bg-amber-50 border-2 border-amber-200 text-amber-800 px-5 py-4 text-sm">
    <p class="font-bold">Absen yang perlu keputusanmu</p>
    <p class="text-xs mt-0.5">Siswa yang memilih tidak hadir (Izin/Sakit/Lainnya) atau absen dari luar radius sekolah ({{ $sekolah->radius_meter }} m) muncul di sini. Absen baru dihitung setelah kamu menyetujuinya.</p>
</div>

@forelse($menunggu as $namaKelas => $daftar)
<div class="mb-8">
<<<<<<< HEAD
    <h3 class="text-sm font-bold text-teal-800 bg-teal-100 inline-block px-3 py-1 rounded-full mb-3">🏫 Kelas {{ $namaKelas }} <span class="opacity-70 font-semibold">({{ $daftar->count() }} menunggu)</span></h3>
=======
    <h3 class="text-sm font-bold text-teal-800 bg-teal-100 inline-block px-3 py-1 rounded-full mb-3"><x-ikon nama="users" class="w-4 h-4 inline-block align-[-2px] mr-1" /> Kelas {{ $namaKelas }} <span class="opacity-70 font-semibold">({{ $daftar->count() }} menunggu)</span></h3>
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($daftar as $a)
        @php $jr = $a->jarakTerhadap($sekolah); @endphp
        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-4 kartu flex gap-4">
            @if($a->foto_wajah)
                <img src="{{ asset('storage/'.$a->foto_wajah) }}" @click="modalFoto = '{{ asset('storage/'.$a->foto_wajah) }}'"
                     class="w-24 h-24 rounded-xl object-cover cursor-pointer shrink-0">
            @else
                <div class="w-24 h-24 rounded-xl bg-slate-100 shrink-0 flex items-center justify-center text-slate-300 text-xs">Tanpa foto</div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="font-bold text-ink-900 truncate">{{ $a->siswa->nama ?? '-' }}</p>
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d M Y') }}, {{ \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') }} WIB • Keterangan: <b>{{ $a->status }}</b></p>
                @if($a->keterangan_detail)<p class="text-xs text-slate-400 italic">"{{ $a->keterangan_detail }}"</p>@endif
                @if($jr)
<<<<<<< HEAD
                    <p class="text-xs text-slate-500 mt-1">📍 {{ number_format($jr['efektif'], 0, ',', '.') }} m dari sekolah
                        <a href="{{ $a->tautanGoogleMaps() }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">(peta)</a></p>
                @else
                    <p class="text-xs text-slate-400 mt-1">📍 Lokasi tidak tersedia</p>
=======
                    <p class="text-xs text-slate-500 mt-1"><x-ikon nama="map-pin" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ number_format($jr['efektif'], 0, ',', '.') }} m dari sekolah
                        <a href="{{ $a->tautanGoogleMaps() }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">(peta)</a></p>
                @else
                    <p class="text-xs text-slate-400 mt-1"><x-ikon nama="map-pin" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Lokasi tidak tersedia</p>
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
                @endif
                @if($a->alasan_menunggu)<p class="text-[11px] text-amber-700 bg-amber-50 rounded-lg px-2 py-1 mt-1.5">{{ $a->alasan_menunggu }}</p>@endif

                <div class="mt-3 flex flex-wrap gap-2 items-start" x-data="{ tolak: false }">
                    <form method="POST" action="{{ route('absensi.setujui', $a) }}">
                        @csrf
                        <button class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">Setujui</button>
                    </form>
                    <button type="button" @click="tolak = !tolak" class="px-4 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-bold hover:bg-rose-200 transition">Tolak</button>
                    <form x-show="tolak" x-cloak method="POST" action="{{ route('absensi.tolak', $a) }}" class="w-full flex gap-2">
                        @csrf
                        <input type="text" name="catatan_persetujuan" maxlength="255" placeholder="Alasan penolakan (opsional)"
                               class="flex-1 min-w-0 rounded-lg border-2 border-slate-300 px-2 py-1.5 text-xs focus:ring-2 focus:ring-rose-400 outline-none">
                        <button class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
    <div class="text-center py-16 bg-white rounded-2xl border-2 border-slate-200">
<<<<<<< HEAD
        <span class="text-4xl">🎉</span>
=======
        <span class="text-4xl"><x-ikon nama="check-circle" class="w-9 h-9" /></span>
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        <p class="mt-2 font-bold text-ink-900">Tidak ada absen yang menunggu</p>
        <p class="text-sm text-slate-400">Absen baru yang butuh persetujuan akan muncul di halaman ini.</p>
    </div>
@endforelse

@if($riwayat->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <h3 class="font-bold text-ink-900 mb-3">Keputusan terakhir</h3>
    <div class="space-y-2">
        @foreach($riwayat as $r)
        <div class="flex items-center justify-between text-sm border-b border-slate-50 pb-2">
            <span><b>{{ $r->siswa->nama ?? '-' }}</b> <span class="text-slate-400">({{ $r->siswa->kelas->nama_kelas ?? '-' }})</span></span>
            <span class="text-xs font-bold {{ $r->status_persetujuan === 'disetujui' ? 'text-emerald-600' : 'text-rose-600' }}">{{ ucfirst($r->status_persetujuan) }}
                <span class="text-slate-400 font-normal">• {{ $r->diputuskan_pada?->translatedFormat('d M, H:i') }}</span></span>
        </div>
        @endforeach
    </div>
</div>
@endif

<div x-show="modalFoto" x-cloak @click.self="modalFoto = null"
     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <img :src="modalFoto" class="w-full rounded-2xl shadow-2xl">
        <button @click="modalFoto = null" class="mt-3 mx-auto block bg-white/90 rounded-xl px-5 py-2 text-sm font-bold">Tutup</button>
    </div>
</div>
</div>
@endsection
