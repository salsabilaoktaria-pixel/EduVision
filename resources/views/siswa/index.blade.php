@extends('layouts.app')
@section('title', 'Data Siswa')
@section('content')
<x-tombol-kembali :first-page="true" />

@php
    // Kalau lagi mencari, tab yang aktif pertama kali harus tingkat yang BENAR-BENAR
    // punya hasil (bukan selalu 'X') — sebelumnya ini yang bikin pencarian siswa
    // kelas XII kelihatan "nyasar" ke tab X yang kosong.
    $tabAwalKey = null;
    foreach ($siswaPerTingkat as $tingkatKey => $daftar) {
        if ($daftar->count() > 0) { $tabAwalKey = $tingkatKey; break; }
    }
    $tabAwalKey = $tabAwalKey ?? ($siswaPerTingkat->keys()->first() ?? 'X');
@endphp
<div x-data="{ tab: '{{ $tabAwalKey }}' }">

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" id="formCariSiswa" class="flex gap-2">
            <input type="text" name="cari" id="inputCariSiswa" value="{{ request('cari') }}" placeholder="Cari nama / NISN..."
                class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm w-56 focus:ring-2 focus:ring-teal-500 outline-none">
            <button class="bg-slate-100 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200">Cari</button>
        </form>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('siswa.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif">+ Tambah Siswa</a>
        @endif
    </div>

    <!-- Tab per tingkat kelas -->
    <div class="flex gap-2 mb-5 border-b border-slate-100 overflow-x-auto">
        @foreach($siswaPerTingkat as $tingkat => $daftarSiswa)
            <button @click="tab = '{{ $tingkat }}'"
                :class="tab === '{{ $tingkat }}' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-bold border-b-2 whitespace-nowrap transition">
                Kelas {{ $tingkat }} <span class="text-xs font-normal">({{ $daftarSiswa->count() }})</span>
            </button>
        @endforeach
    </div>

    @foreach($siswaPerTingkat as $tingkat => $daftarSiswa)
    <div x-show="tab === '{{ $tingkat }}'" x-cloak class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 border-b border-slate-100">
                    <th class="py-3 pr-4 font-semibold">NISN</th>
                    <th class="py-3 pr-4 font-semibold">Nama</th>
                    <th class="py-3 pr-4 font-semibold">Kelas</th>
                    <th class="py-3 pr-4 font-semibold">Jenis Kelamin</th>
                    <th class="py-3 pr-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarSiswa as $s)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-3 pr-4 font-medium text-slate-700">{{ $s->nisn }}</td>
                    <td class="py-3 pr-4">
                        <a href="{{ route('siswa.show', $s) }}" class="font-semibold text-ink-900 hover:text-teal-600">{{ $s->nama }}</a>
                    </td>
                    <td class="py-3 pr-4 text-slate-600">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-3 pr-4 text-slate-600">{{ $s->jenis_kelamin ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('siswa.show', $s) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">Detail</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('siswa.edit', $s) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                            <form method="POST" action="{{ route('siswa.destroy', $s) }}" onsubmit="return confirm('Hapus data siswa ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-10 text-slate-400">Belum ada siswa di kelas {{ $tingkat }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach
</div>
</div>

<script>
    // Pencarian TIDAK auto-reload lagi — baru submit saat tombol "Cari" diklik
    // atau tombol Enter ditekan (form submit normal), sesuai permintaan.
</script>
@endsection
