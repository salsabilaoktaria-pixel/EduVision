@extends('layouts.app')
@section('title', 'Edit Mata Pelajaran')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <form method="POST" action="{{ route('mapel.update', $mapel) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="text-sm font-semibold text-slate-700">Kode Mapel</label><input type="text" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Nama Mapel</label><input type="text" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Guru Pengampu</label>
            <select name="guru_id" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)<option value="{{ $g->id }}" @selected($mapel->guru_id == $g->id)>{{ $g->nama }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">KKM</label><input type="number" name="kkm" value="{{ old('kkm', $mapel->kkm) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Update</button>
            <a href="{{ route('mapel.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>

<!-- Fitur: Mapel untuk Perkelas -->
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl mt-5 kartu">
    <h3 class="font-bold text-ink-900 mb-1">Diajarkan di Kelas Mana Saja?</h3>
    <p class="text-xs text-slate-500 mb-4">Centang kelas yang mendapat pelajaran {{ $mapel->nama_mapel }}. Berguna untuk memisahkan kurikulum tiap tingkat/jurusan.</p>
    <form method="POST" action="{{ route('mapel.assignKelas', $mapel) }}" class="space-y-3">
        @csrf
        <div class="grid sm:grid-cols-3 gap-2">
            @php $kelasTerpilih = $mapel->kelas->pluck('id')->toArray(); @endphp
            @foreach($kelas as $k)
                <label class="flex items-center gap-2 text-sm border-2 border-slate-300 rounded-lg px-3 py-2 cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" @checked(in_array($k->id, $kelasTerpilih)) class="rounded border-slate-300 text-teal-600">
                    {{ $k->nama_kelas }}
                </label>
            @endforeach
        </div>
        <button class="bg-indigo-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-indigo-700 transition tombol-aktif">Simpan Penempatan Kelas</button>
    </form>
</div>
@endsection
