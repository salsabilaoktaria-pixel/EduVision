@extends('layouts.app')
@section('title', 'Input Nilai')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="kelas_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
            </select>
            <select name="siswa_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Siswa --</option>
                @foreach($siswaList as $s)<option value="{{ $s->id }}" @selected(request('siswa_id')==$s->id)>{{ $s->nama }}</option>@endforeach
            </select>
            @if(request('kelas_id') || request('siswa_id'))
                <a href="{{ route('nilai.index') }}" class="px-4 py-2 text-sm text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>
        <a href="{{ route('nilai.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif">+ Input Nilai</a>
    </div>

    @forelse($siswaPerKelas as $namaKelas => $daftarSiswa)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-teal-700 bg-teal-50 inline-block px-3 py-1 rounded-full mb-3">{{ $namaKelas }}</h3>
            <div class="space-y-3">
                @foreach($daftarSiswa as $siswa)
                    <div class="border-2 border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-slate-50 px-4 py-2.5 font-bold text-ink-900 text-sm">{{ $siswa->nama }}</div>
                        <table class="w-full text-sm">
                            <tbody>
                            @foreach($siswa->nilai as $n)
                                <tr class="border-t border-slate-50 hover:bg-slate-50/60">
                                    <td class="py-2.5 pl-4 pr-4 text-slate-600 w-1/4">{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                                    <td class="py-2.5 pr-4 text-slate-600">{{ $n->semester }} - {{ $n->tahun_ajaran }}</td>
                                    <td class="py-2.5 pr-4 text-right font-bold {{ $n->nilai_akhir >= ($n->mataPelajaran->kkm ?? 75) ? 'text-emerald-600' : 'text-rose-600' }}">{{ $n->nilai_akhir }}</td>
                                    <td class="py-2.5 pr-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('nilai.tugas', $n) }}" class="px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-semibold hover:bg-indigo-200">Tugas Harian</a>
                                            <a href="{{ route('nilai.edit', $n) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                                            <form method="POST" action="{{ route('nilai.destroy', $n) }}" onsubmit="return confirm('Hapus nilai ini?')">
                                                @csrf @method('DELETE')
                                                <button class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">Belum ada data nilai.</p>
    @endforelse
</div>
@endsection
