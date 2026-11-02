@extends('layouts.app')
@section('title', 'Edit Tagihan SPP')
@section('content')
{{-- Alamat tombol kembali dikunci ke halaman Detail (bukan url()->previous()) karena halaman ini
     mengarahkan ulang ke Detail setelah simpan. --}}
<x-tombol-kembali :href="route('spp.show', $spp)" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu">
    <p class="text-sm text-slate-500 mb-4">Siswa: <span class="font-bold text-ink-900">{{ $spp->siswa->nama ?? '-' }}</span> ({{ $spp->siswa->kelas->nama_kelas ?? '-' }})</p>

    <form method="POST" action="{{ route('spp.update', $spp) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-slate-700">Bulan</label>
                <select name="bulan" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                        <option value="{{ $b }}" @selected(old('bulan', $spp->bulan) === $b)>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">Tahun</label>
                <input type="text" name="tahun" value="{{ old('tahun', $spp->tahun) }}" required maxlength="4"
                       class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>
        </div>

        <div><label class="text-sm font-semibold text-slate-700">Nominal SPP (Rp)</label>
            <input type="number" name="jumlah" min="0" value="{{ old('jumlah', (int) $spp->jumlah) }}" required
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></div>

        <div><label class="text-sm font-semibold text-slate-700">Status Pembayaran</label>
            <select name="status" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @foreach(['Belum Lunas', 'Lunas'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $spp->status) === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif"><x-ikon nama="check" class="w-4 h-4" /> Simpan Perubahan</button>
            <a href="{{ route('spp.show', $spp) }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition btn-sekunder"><x-ikon nama="x" class="w-4 h-4" /> Batal</a>
        </div>
    </form>
</div>
@endsection
