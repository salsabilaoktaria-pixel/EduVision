@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-3xl kartu">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-ink-900">Detail Kelas</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</p>
        </div>
        <a href="{{ route('kelas.index') }}" class="bg-slate-100 text-slate-700 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200 transition">← Kembali</a>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-slate-50 rounded-xl p-4">
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Tingkat</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $kelas->tingkat }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Nama Kelas</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $kelas->nama_kelas }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Wali Kelas</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $kelas->waliKelas->nama ?? 'Belum ditentukan' }}</p>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Jumlah Siswa</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $kelas->siswa->count() }}</p>
        </div>
    </div>

    @if($kelas->waliKelas)
    <div class="mb-6 p-4 bg-brand-50 rounded-xl border border-brand-100">
        <h4 class="font-semibold text-brand-800 mb-2">Informasi Wali Kelas</h4>
        <div class="grid sm:grid-cols-2 gap-2 text-sm">
            <div><span class="text-slate-500">NIP:</span> <span class="font-medium ml-2">{{ $kelas->waliKelas->nip }}</span></div>
            <div><span class="text-slate-500">No HP:</span> <span class="font-medium ml-2">{{ $kelas->waliKelas->no_hp ?? '-' }}</span></div>
            <div><span class="text-slate-500">Email:</span> <span class="font-medium ml-2">{{ $kelas->waliKelas->user->email ?? '-' }}</span></div>
        </div>
    </div>
    @endif

    <h4 class="font-semibold text-ink-900 mb-3">Daftar Siswa ({{ $kelas->siswa->count() }})</h4>
    @if($kelas->siswa->isEmpty())
        <p class="text-slate-400 text-center py-6">Belum ada siswa di kelas ini.</p>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-2 pr-4 font-semibold">NISN</th><th class="py-2 pr-4 font-semibold">Nama</th>
                <th class="py-2 pr-4 font-semibold">Jenis Kelamin</th><th class="py-2 pr-4 font-semibold text-right">Aksi</th>
            </tr></thead>
            <tbody>
            @foreach($kelas->siswa as $s)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                    <td class="py-2 pr-4 font-medium">{{ $s->nisn }}</td>
                    <td class="py-2 pr-4 font-semibold text-ink-900">{{ $s->nama }}</td>
                    <td class="py-2 pr-4 text-slate-600">{{ $s->jenis_kelamin ?? '-' }}</td>
                    <td class="py-2 pr-4 text-right">
                        <a href="{{ route('siswa.show', $s) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">Detail</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
        <a href="{{ route('kelas.edit', $kelas) }}" class="bg-amber-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-amber-700 transition tombol-aktif">Edit</a>
        <form method="POST" action="{{ route('kelas.destroy', $kelas) }}" onsubmit="return confirm('Hapus kelas ini?')">
            @csrf @method('DELETE')
            <button class="bg-rose-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-rose-700 transition tombol-aktif">Hapus</button>
        </form>
    </div>
</div>
@endsection