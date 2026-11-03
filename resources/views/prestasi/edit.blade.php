@extends('layouts.app')
@section('title', 'Edit Prestasi')
@section('content')
<x-tombol-kembali :href="route('prestasi.index')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    @if($prestasi->file_bukti)
        <div class="mb-4">
            <p class="text-xs text-slate-500 mb-2">File bukti saat ini:</p>
            @if(preg_match('/\.(jpg|jpeg|png)$/i', $prestasi->file_bukti))
                <img src="{{ asset('storage/'.$prestasi->file_bukti) }}" class="w-24 h-24 rounded-xl object-cover ring-1 ring-slate-200">
            @else
                <a href="{{ asset('storage/'.$prestasi->file_bukti) }}" target="_blank" class="text-teal-600 text-sm font-semibold underline">Lihat file PDF</a>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('prestasi.update', $prestasi) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="text-sm font-semibold text-slate-700">Siswa</label>
            <select name="siswa_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @foreach($siswa as $s)<option value="{{ $s->id }}" @selected($prestasi->siswa_id==$s->id)>{{ $s->nama }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Nama Prestasi</label><input type="text" name="nama_prestasi" value="{{ old('nama_prestasi', $prestasi->nama_prestasi) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Tingkat</label>
                <select name="tingkat" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Sekolah','Kecamatan','Kota/Kabupaten','Provinsi','Nasional','Internasional'] as $t)
                        <option value="{{ $t }}" @selected($prestasi->tingkat==$t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Peringkat</label><input type="text" name="peringkat" value="{{ old('peringkat', $prestasi->peringkat) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Tanggal</label><input type="date" name="tanggal" value="{{ old('tanggal', $prestasi->tanggal) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        <div><label class="text-sm font-semibold text-slate-700">Ganti File Bukti (opsional)</label><input type="file" name="file_bukti" class="mt-1 w-full text-sm"></div>
        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Update</button>
            <a href="{{ route('prestasi.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
