@extends('layouts.app')
<<<<<<< HEAD
@section('title', 'Hasil Scan Absen Mapel')
@section('content')
<div class="max-w-md mx-auto mt-6">
@if($berhasil)
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-8 text-center text-white shadow-lg shadow-emerald-600/20">
        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto animate-[ping_1s_ease-out_1]"></div>
        <div class="w-20 h-20 -mt-20 rounded-full bg-white flex items-center justify-center mx-auto text-4xl">✅</div>
        <h2 class="mt-5 font-extrabold text-xl">Absen Berhasil!</h2>
        <p class="text-sm text-emerald-50 mt-1">{{ $pesan }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 mt-4 kartu">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Detail Kehadiran</span>
            <span class="ml-auto px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">
                {{ ($sumber ?? 'qr') === 'qr' ? '📷 Via Scan QR' : '⌨️ Kode Manual' }}
            </span>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Mata Pelajaran</dt><dd class="font-semibold text-ink-900">{{ $sesi->mataPelajaran->nama_mapel ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Kelas</dt><dd class="font-semibold text-ink-900">{{ $sesi->kelas->nama_kelas ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jam Ke</dt><dd class="font-semibold text-ink-900">{{ $sesi->labelJamKe() }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Waktu Tercatat</dt><dd class="font-semibold text-ink-900">{{ now()->translatedFormat('d F Y, H:i') }}</dd></div>
        </dl>
    </div>

    <a href="{{ route('dashboard') }}" class="mt-4 block text-center bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kembali ke Dashboard</a>
@else
    <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-8 text-center text-white shadow-lg shadow-rose-600/20">
        <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center mx-auto text-4xl">⚠️</div>
        <h2 class="mt-5 font-extrabold text-xl">Absen Tidak Tercatat</h2>
        <p class="text-sm text-rose-50 mt-1">{{ $pesan }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 mt-4 kartu">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Coba Langkah Ini</p>
        <ul class="text-sm text-slate-600 space-y-1.5 list-disc list-inside">
            <li>Minta guru menampilkan QR / kode terbaru (berganti tiap beberapa detik).</li>
            <li>Pastikan kamu memindai QR untuk kelas & mapel yang sesuai jadwalmu.</li>
            <li>Kalau kamera bermasalah, gunakan <a href="{{ route('absensimapel.formManual') }}" class="text-teal-600 font-semibold underline">input kode manual</a>.</li>
        </ul>
    </div>

    <div class="flex gap-2 mt-4">
        <a href="{{ route('absensimapel.formManual') }}" class="flex-1 text-center bg-slate-100 text-ink-900 rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-200 transition tombol-aktif">Input Kode Manual</a>
        <a href="{{ route('dashboard') }}" class="flex-1 text-center bg-teal-600 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Dashboard</a>
    </div>
@endif
=======
@section('title', 'Hasil Absen Mapel')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-8 text-center mt-6 kartu">
    <span class="text-6xl">{{ $berhasil ? '✅' : '⚠️' }}</span>
    <p class="mt-4 font-bold text-lg {{ $berhasil ? 'text-emerald-700' : 'text-rose-600' }}">{{ $pesan }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $sesi->mataPelajaran->nama_mapel ?? '' }} — {{ $sesi->kelas->nama_kelas ?? '' }}</p>
    <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kembali ke Dashboard</a>
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
</div>
@endsection
