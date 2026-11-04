@extends('layouts.app')
@section('title', 'Deteksi Membolos')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="mb-5">
        <h2 class="font-extrabold text-lg text-ink-900">🚩 Deteksi Terindikasi Membolos</h2>
        <p class="text-sm text-slate-500">Siswa yang <b>Hadir</b> di absen Face ID pagi ({{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}) tapi <b>Tanpa Keterangan</b> di salah satu sesi absen mapel hari ini (tidak scan QR dalam 5 menit pertama jam pelajaran).</p>
    </div>

    @forelse($flagged as $siswaId => $daftar)
        @php $siswa = $daftar->first()->siswa; @endphp
        <div class="border border-rose-100 bg-rose-50/50 rounded-xl p-4 mb-3">
            <p class="font-bold text-ink-900">{{ $siswa->nama ?? '-' }} <span class="text-xs font-normal text-slate-500">({{ $siswa->kelas->nama_kelas ?? '-' }})</span></p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($daftar as $a)
                    <span class="px-3 py-1 rounded-full bg-rose-600 text-white text-xs font-bold">
                        Terindikasi Membolos Jam Ke-{{ $a->sesi->labelJamKe() }} ({{ $a->sesi->mataPelajaran->nama_mapel ?? '-' }})
                    </span>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center py-12 text-slate-400">✅ Tidak ada siswa yang terindikasi membolos hari ini.</p>
    @endforelse
</div>
@endsection
