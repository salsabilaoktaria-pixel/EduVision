@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <select name="kelas_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
            </select>
        </form>
        <a href="{{ route('jadwal.create') }}" class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition text-center tombol-aktif">+ Tambah Jadwal</a>
    </div>

    @forelse($jadwal as $hari => $daftar)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-700 bg-indigo-50 inline-block px-3 py-1 rounded-full mb-3">{{ $hari }}</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-slate-500 border-b border-slate-100">
                        <th class="py-2 pr-4 font-semibold">Jam Ke</th>
                        <th class="py-2 pr-4 font-semibold">Jam</th>
                        <th class="py-2 pr-4 font-semibold">Kelas</th>
                        <th class="py-2 pr-4 font-semibold">Mapel</th>
                        <th class="py-2 pr-4 font-semibold">Guru</th>
                        <th class="py-2 pr-4 font-semibold text-right">Aksi</th>
                    </tr></thead>
                    <tbody>
                    @foreach($daftar as $j)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                            <td class="py-2.5 pr-4 font-bold text-ink-900">{{ $j->labelJamKe() }}</td>
                            <td class="py-2.5 pr-4 text-slate-600">{{ $j->labelJamClock() }}</td>
                            <td class="py-2.5 pr-4">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-2.5 pr-4">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</td>
                            <td class="py-2.5 pr-4">{{ $j->guru->nama ?? '-' }}</td>
                            <td class="py-2.5 pr-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('jadwal.edit', $j) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200">Edit</a>
                                    <form method="POST" action="{{ route('jadwal.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal ini?')" class="inline">
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
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">Belum ada jadwal pelajaran. Tambahkan dulu supaya fitur Absen QR Per Mapel bisa dipakai.</p>
    @endforelse
</div>
@endsection
