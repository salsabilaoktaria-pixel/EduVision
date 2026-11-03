@extends('layouts.app')
@section('title', 'Absen QR Mapel')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-8 text-center mt-6 kartu">
    <span class="text-5xl">📶</span>
    <h2 class="mt-3 font-extrabold text-lg text-ink-900">Absen Mapel — Input Kode</h2>
    <p class="text-sm text-slate-500 mt-1 mb-5">Biasanya kamu tinggal scan QR di layar guru. Kalau kameramu bermasalah (atau lagi coba-coba/testing), masukkan 8 karakter kode yang tampil di layar guru di bawah ini.</p>

    <form method="POST" action="{{ route('absensimapel.prosesManual') }}">
        @csrf
        <input type="text" name="kode" maxlength="8" required autofocus placeholder="CONTOH: A1B2C3D4"
               class="w-full text-center tracking-[0.3em] uppercase font-black text-xl rounded-xl border-2 border-slate-300 px-4 py-3 focus:ring-2 focus:ring-teal-500 outline-none">
        <button class="mt-4 w-full bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Kirim Absen</button>
    </form>

    <p class="text-xs text-slate-400 mt-5">Kodenya berganti tiap beberapa detik — kalau ditolak "kode salah/kadaluarsa", minta guru menampilkan kode terbaru dan coba lagi.</p>
</div>
@endsection
