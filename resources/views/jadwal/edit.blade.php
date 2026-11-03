@extends('layouts.app')
@section('title', 'Edit Jadwal Pelajaran')
@section('content')
<x-tombol-kembali />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <form method="POST" action="{{ route('jadwal.update', $jadwal) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Kelas</label>
                <select name="kelas_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)<option value="{{ $k->id }}" @selected(old('kelas_id', $jadwal->kelas_id)==$k->id)>{{ $k->nama_kelas }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Hari</label>
                <select name="hari" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                        <option value="{{ $h }}" @selected(old('hari', $jadwal->hari)==$h)>{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id', $jadwal->mata_pelajaran_id)==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Guru Pengajar</label>
                <select name="guru_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)<option value="{{ $g->id }}" @selected(old('guru_id', $jadwal->guru_id)==$g->id)>{{ $g->nama }}</option>@endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Jam Ke <span class="text-rose-500">*</span></label>
            <p class="text-xs text-slate-400 mb-2">Boleh pilih lebih dari satu kalau mapel ini memang berlangsung beberapa jam berurutan (mis. Matematika jam ke 1–3), supaya guru tidak perlu buka sesi absen QR berkali-kali di jam yang sama.</p>
            <div class="grid grid-cols-6 sm:grid-cols-12 gap-2">
                @for($i = 1; $i <= 12; $i++)
                    <label class="flex items-center justify-center rounded-xl border-2 border-slate-300 py-2 text-sm font-semibold cursor-pointer has-[:checked]:bg-teal-600 has-[:checked]:text-white has-[:checked]:border-teal-600 transition">
                        <input type="checkbox" name="jam_ke[]" value="{{ $i }}" class="hidden" @checked(in_array($i, old('jam_ke', $jadwal->jam_ke)))>
                        {{ $i }}
                    </label>
                @endfor
            </div>
            @error('jam_ke')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Jam Mulai <span class="text-rose-500">*</span></label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai ?? '', 0, 5)) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('jam_mulai')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Jam Selesai <span class="text-rose-500">*</span></label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai ?? '', 0, 5)) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('jam_selesai')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <p class="text-xs text-slate-400 -mt-2">Jam mulai & selesai wajib diisi, contoh: jam ke 1-2 = 07.00 sampai 08.30. Ini akan ditampilkan ke siswa & guru di jadwal mereka.</p>

        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Simpan Perubahan</button>
            <a href="{{ route('jadwal.index') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
