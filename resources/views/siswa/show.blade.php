@extends('layouts.app')
@section('title', 'Detail Siswa')

@section('content')
<x-tombol-kembali />
<div x-data="{ modalFoto: null }" @keydown.escape.window="modalFoto = null">
<div class="grid lg:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 lg:col-span-1 kartu">
        @if($siswa->foto)
            <img src="{{ asset('storage/'.$siswa->foto) }}" alt="Foto {{ $siswa->nama }}"
                 @click="modalFoto = '{{ asset('storage/'.$siswa->foto) }}'"
                 class="w-24 h-24 rounded-2xl object-cover mb-4 ring-1 ring-slate-200 cursor-pointer hover:opacity-90 transition">
        @else
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-teal-400 to-teal-600 text-white flex items-center justify-center text-3xl font-extrabold mb-4">
                {{ strtoupper(substr($siswa->nama,0,1)) }}
            </div>
        @endif
        <h2 class="text-xl font-extrabold text-ink-900">{{ $siswa->nama }}</h2>
        <p class="text-sm text-slate-500 mb-4">NISN: {{ $siswa->nisn }}</p>
        <div class="space-y-2 text-sm">
            <p><span class="text-slate-500">Kelas:</span> <span class="font-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></p>
            <p><span class="text-slate-500">Jenis Kelamin:</span> <span class="font-semibold">{{ $siswa->jenis_kelamin ?? '-' }}</span></p>
            <p><span class="text-slate-500">Tanggal Lahir:</span> <span class="font-semibold">{{ $siswa->tanggal_lahir ?? '-' }}</span></p>
            <p><span class="text-slate-500">Alamat:</span> <span class="font-semibold">{{ $siswa->alamat ?? '-' }}</span></p>
            <p><span class="text-slate-500">Wali:</span> <span class="font-semibold">{{ $siswa->nama_wali ?? '-' }} ({{ $siswa->no_hp_wali ?? '-' }})</span></p>
        </div>
        <a href="{{ route('rapor.show', $siswa) }}" class="mt-5 block text-center bg-teal-600 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Lihat Rapor & Prediksi</a>
    </div>

    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
            <h3 class="font-bold text-ink-900 mb-3">Nilai Terbaru</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-slate-500 border-b"><th class="py-2">Mapel</th><th>Semester</th><th class="text-right">Nilai Akhir</th></tr></thead>
                <tbody>
                @forelse($siswa->nilai as $n)
                    <tr class="border-b border-slate-50">
                        <td class="py-2">{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                        <td>{{ $n->semester }} - {{ $n->tahun_ajaran }}</td>
                        <td class="text-right font-bold">{{ $n->nilai_akhir }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-6 text-slate-400">Belum ada nilai.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
            <h3 class="font-bold text-ink-900 mb-3">Riwayat Prestasi</h3>
            <div class="space-y-2">
                @forelse($siswa->prestasi as $p)
                    <div class="flex justify-between text-sm border-b border-slate-50 pb-2">
                        <span>🏆 {{ $p->nama_prestasi }} <span class="text-slate-400">({{ $p->tingkat }})</span></span>
                        <span class="text-slate-500">{{ $p->tanggal }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada prestasi tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div x-show="modalFoto" x-cloak @click.self="modalFoto = null"
     class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <img :src="modalFoto" class="w-full rounded-2xl shadow-2xl">
        <button @click="modalFoto = null" class="mt-3 mx-auto block bg-white/90 rounded-xl px-5 py-2 text-sm font-bold">Tutup</button>
    </div>
</div>
</div>
@endsection
