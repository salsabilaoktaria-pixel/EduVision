@extends('layouts.app')
@section('title', 'Pengumpulan Tugas')
@section('content')
<x-tombol-kembali :first-page="true" />

<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <p class="text-sm text-slate-500">Daftar tugas yang sudah dibuat, beserta jumlah siswa yang sudah mengumpulkan.</p>
        <div class="flex gap-2">
            <a href="{{ route('tugas.kalender') }}" class="tombol-aktif bg-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-indigo-700 transition text-center">🗓️ Kalender</a>
            <a href="{{ route('tugas.create') }}" class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition text-center">+ Buat Tugas</a>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tugas as $t)
            @php
                $totalSiswa = $t->kelas->siswa->count() ?? 0;
                $sudahKumpul = $t->pengumpulan->count();
                $lewatDeadline = $t->sudahLewatDeadline();
            @endphp
            <div class="kartu border-2 border-slate-200 rounded-xl p-4 hover:border-teal-300 transition">
                <a href="{{ route('tugas.show', $t) }}" class="block">
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs font-bold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $lewatDeadline ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $lewatDeadline ? 'Ditutup' : 'Terbuka' }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $t->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                    <p class="font-bold text-ink-900 mt-2">{{ $t->judul }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $t->mataPelajaran->nama_mapel ?? '-' }}</p>
                    <p class="text-xs text-slate-400 mt-2">Deadline: {{ $t->deadline->translatedFormat('d M Y, H:i') }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-teal-500" style="width: {{ $totalSiswa > 0 ? round($sudahKumpul/$totalSiswa*100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs font-semibold text-slate-600">{{ $sudahKumpul }}/{{ $totalSiswa }}</span>
                    </div>
                </a>
                <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <a href="{{ route('tugas.edit', $t) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                    <form method="POST" action="{{ route('tugas.destroy', $t) }}" onsubmit="return confirm('Hapus tugas ini beserta semua pengumpulannya?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center py-12 text-slate-400">Belum ada tugas yang dibuat.</p>
        @endforelse
    </div>
</div>
@endsection
