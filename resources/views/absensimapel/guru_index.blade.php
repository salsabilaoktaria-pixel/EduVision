@extends('layouts.app')
@section('title', 'Absen QR Per Mapel')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-extrabold text-lg text-ink-900">Jadwal Mengajar Hari Ini</h2>
            <p class="text-sm text-slate-500">{{ $hariIni }}, {{ now()->translatedFormat('d F Y') }}</p>
        </div>
        <a href="{{ route('jadwal.index') }}" class="text-sm font-semibold text-teal-600 hover:underline">Kelola Jadwal →</a>
    </div>

    @forelse($jadwalHariIni as $j)
        @php $sesi = $sesiHariIni->get($j->id); @endphp
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-2 border-slate-200 rounded-xl p-4 mb-3">
            <div>
                <p class="font-bold text-ink-900">{{ $j->mataPelajaran->nama_mapel ?? '-' }} — {{ $j->kelas->nama_kelas ?? '-' }}</p>
                <p class="text-xs text-slate-500">Jam ke {{ $j->labelJamKe() }} • Guru: {{ $j->guru->nama ?? '-' }}</p>
                @if($sesi)
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $sesi->status === 'dibuka' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $sesi->status === 'dibuka' ? '🟢 Sesi sedang dibuka' : '⚫ Sesi sudah ditutup' }}
                    </span>
                @endif
            </div>
            <div class="flex gap-2">
                @if($sesi && $sesi->status === 'dibuka')
                    <a href="{{ route('absensimapel.layar', $sesi) }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition whitespace-nowrap tombol-aktif">Lanjutkan Sesi →</a>
                @else
                    <form method="POST" action="{{ route('absensimapel.buka', $j) }}">
                        @csrf
                        <button class="bg-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-indigo-700 transition whitespace-nowrap tombol-aktif">📶 Buka Absen QR</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">Tidak ada jadwal mengajar untuk hari {{ $hariIni }}. <a href="{{ route('jadwal.create') }}" class="text-teal-600 font-semibold hover:underline">Tambah jadwal</a> dulu kalau belum ada.</p>
    @endforelse
</div>
@endsection
