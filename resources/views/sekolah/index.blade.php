@extends('layouts.app')
@section('title', 'Kelola Sekolah')
@section('content')
<div class="flex items-center justify-between gap-3 flex-wrap mb-5">
    <p class="text-sm text-slate-500">Setiap sekolah punya data, akun, logo, dan lokasi absen sendiri-sendiri.</p>
    <a href="{{ route('sekolah.create') }}" data-popup class="tombol-aktif bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold transition"><x-ikon nama="school" class="w-4 h-4 inline-block align-[-2px] mr-1" /> Tambah Sekolah</a>
</div>
<div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase"><th class="py-3 pl-3">Sekolah</th><th>Kota</th><th class="text-center">Siswa</th><th class="text-center">Guru</th><th class="text-center">Kelas</th><th class="text-right pr-3">Aksi</th></tr></thead>
        <tbody>
        @forelse($sekolah as $s)
            <tr class="border-t border-slate-100">
                <td class="py-3 pl-3">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl border-2 border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                            @if($s->logoUrl())<img src="{{ $s->logoUrl() }}" class="w-full h-full object-contain p-0.5" alt="">@else<x-ikon nama="school" class="w-5 h-5 text-slate-400" />@endif
                        </span>
                        <span><span class="block font-bold text-ink-900">{{ $s->nama }}</span><span class="block text-xs text-slate-500">{{ $s->jenjang }} @if($s->npsn) • NPSN {{ $s->npsn }} @endif</span></span>
                    </div>
                </td>
                <td>{{ $s->kota ?: '-' }}</td>
                <td class="text-center">{{ $jumlah['siswa'][$s->id] ?? 0 }}</td>
                <td class="text-center">{{ $jumlah['guru'][$s->id] ?? 0 }}</td>
                <td class="text-center">{{ $jumlah['kelas'][$s->id] ?? 0 }}</td>
                <td class="text-right pr-3"><x-aksi :detail="route('sekolah.show', $s)" :edit="route('sekolah.edit', $s)" :hapus="route('sekolah.destroy', $s)" konfirmasi="Hapus sekolah ini BESERTA seluruh siswa, guru, nilai, dan datanya? Tidak bisa dibatalkan." /></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center py-10 text-slate-400">Belum ada sekolah.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
