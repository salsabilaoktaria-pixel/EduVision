@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('content')
{{-- Dipaksa ke route('tugas.index') secara eksplisit (bukan url()->previous()):
     setelah form "beri nilai" di halaman ini disubmit, Laravel menyimpan URL
     halaman ini sendiri sebagai "previous", sehingga tombol kembali versi
     default hanya mereload halaman yang sama alih-alih benar-benar kembali. --}}
<x-tombol-kembali :href="route('tugas.index')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu mb-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-extrabold text-ink-900">{{ $tugas->judul }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }} &middot; {{ $tugas->kelas->nama_kelas ?? '-' }}</p>
        </div>
        <form method="POST" action="{{ route('tugas.destroy', $tugas) }}" onsubmit="return confirm('Hapus tugas ini beserta semua pengumpulannya?')">
            @csrf @method('DELETE')
            <button class="text-xs text-rose-500 hover:text-rose-700 font-semibold bg-rose-50 px-3 py-1.5 rounded-lg">Hapus Tugas</button>
        </form>
    </div>
    @if($tugas->deskripsi)<p class="text-sm text-slate-600 mt-3">{{ $tugas->deskripsi }}</p>@endif
    <p class="text-xs text-slate-400 mt-3">Deadline: <b>{{ $tugas->deadline->translatedFormat('d M Y, H:i') }}</b> &middot; {{ $tugas->sudahLewatDeadline() ? 'Sudah ditutup' : 'Masih terbuka' }}</p>

    @if($tugas->lampiran->isNotEmpty())
        <div class="mt-4 border-t border-slate-100 pt-3">
            <p class="text-xs font-semibold text-slate-500 mb-2">📎 Lampiran Tugas:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($tugas->lampiran as $l)
                    @if($l->tipe === 'file')
                        <a href="{{ asset('storage/'.$l->file_path) }}" target="_blank" class="text-xs font-semibold text-teal-600 bg-teal-50 px-3 py-1.5 rounded-lg hover:bg-teal-100">⬇️ {{ $l->file_nama_asli }}</a>
                    @else
                        <a href="{{ $l->link }}" target="_blank" class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100">🔗 Buka Link</a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <p class="font-bold text-ink-900 mb-4">Status Pengumpulan Siswa</p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="py-2 pr-4 font-semibold">Siswa</th>
                <th class="py-2 pr-4 font-semibold">Status</th>
                <th class="py-2 pr-4 font-semibold">Dikumpulkan</th>
                <th class="py-2 pr-4 font-semibold">Berkas</th>
                <th class="py-2 pr-4 font-semibold">Nilai</th>
            </tr></thead>
            <tbody>
            @foreach($daftarSiswa as $d)
                @php $p = $d['pengumpulan']; @endphp
                <tr class="border-b border-slate-50 hover:bg-slate-50/60 align-top">
                    <td class="py-2.5 pr-4 font-semibold text-ink-900">{{ $d['siswa']->nama }}</td>
                    <td class="py-2.5 pr-4">
                        @if(!$p)
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Belum Dikumpulkan</span>
                        @elseif($p->terlambat())
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">Terlambat</span>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Sudah Dikumpulkan</span>
                        @endif
                    </td>
                    <td class="py-2.5 pr-4 text-slate-500">{{ $p?->dikumpulkan_pada?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                    <td class="py-2.5 pr-4">
                        @if($p && $p->tipe === 'file' && $p->files->isNotEmpty())
                            <div class="flex flex-col gap-1">
                                @foreach($p->files as $f)
                                    <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="text-teal-600 font-semibold hover:underline">⬇️ {{ $f->ekstensiFile() }}</a>
                                @endforeach
                            </div>
                        @elseif($p && $p->tipe === 'link')
                            <a href="{{ $p->link }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">🔗 Buka Link</a>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="py-2.5 pr-4">
                        @if($p)
                            <form method="POST" action="{{ route('tugas.beriNilai', $p) }}" class="flex items-center gap-1.5">
                                @csrf
                                <input type="number" name="nilai" min="0" max="100" value="{{ $p->nilai }}" placeholder="0-100" class="w-16 rounded-lg border-2 border-slate-300 px-2 py-1 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                <button class="text-xs bg-teal-600 text-white rounded-lg px-2.5 py-1.5 font-semibold hover:bg-teal-700">Simpan</button>
                            </form>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
