@extends('layouts.app')
@section('title', 'Kartu Akun Siswa')
@section('content')
<div class="print:hidden"><x-tombol-kembali /></div>
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu print:border-0 print:shadow-none">
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-5 print:hidden">
        <div>
            <label class="text-sm font-semibold text-slate-700">Pilih Kelas</label>
            <select name="kelas_id" class="mt-1 rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm bg-white">
                <option value="">— pilih —</option>
                @foreach($kelas as $k)<option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>@endforeach
            </select>
        </div>
        <button class="tombol-aktif btn-utama"><x-ikon nama="search" class="w-4 h-4" /> Tampilkan</button>
        @if($daftar->count())<button type="button" onclick="window.print()" class="tombol-aktif btn-sekunder"><x-ikon nama="download" class="w-4 h-4" /> Cetak</button>@endif
    </form>
    <p class="text-xs text-slate-500 mb-4 print:hidden">Password yang sudah diganti siswa tidak tampil. Gunakan tombol Reset di halaman detail akun bila perlu.</p>
    <div class="grid sm:grid-cols-2 gap-3">
        @forelse($daftar as $s)
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-sm break-inside-avoid">
                <p class="font-extrabold text-ink-900">{{ $s->nama }}</p>
                <p class="text-xs text-slate-500 mb-2">{{ $s->kelas->nama_kelas ?? '' }} · NISN {{ $s->nisn }}</p>
                <p>Username: <span class="font-bold break-all">{{ $s->user->email ?? '-' }}</span></p>
                <p>Password: <span class="font-mono font-bold">{{ ($s->user && $s->user->passwordAwalTerbaca()) ?: '(sudah diganti)' }}</span></p>
            </div>
        @empty
            <p class="text-sm text-slate-400">Pilih kelas untuk menampilkan kartu akun.</p>
        @endforelse
    </div>
</div>
@endsection
