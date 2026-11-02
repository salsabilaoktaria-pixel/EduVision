@extends('layouts.app')
@section('title', 'Kelola Semester')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="grid lg:grid-cols-3 gap-5">
    <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5 lg:col-span-2">
        <h3 class="font-display text-lg text-ink-900 mb-1">Daftar Semester</h3>
        <p class="text-sm text-slate-500 mb-4">Semester berjalan ditentukan otomatis dari tanggal (Ganjil: Juli–Desember, Genap: Januari–Juni). Daftar ini otomatis mengikuti data: semester yang sudah punya nilai, tugas, materi, jadwal, absensi, SPP, prestasi, atau aspirasi pasti muncul. Semester mendatang ditambahkan lewat tombol atau formulir di samping.</p>
        <div class="divide-y divide-slate-100">
            @foreach($opsi as $s)
                @php $kunci = \App\Support\Semester::kunci($s); $dipilih = $kunci === \App\Support\Semester::kunci(\App\Support\Semester::terpilih()); @endphp
                <div class="py-3 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-ink-900">Semester {{ $s['semester'] }} {{ $s['tahun_ajaran'] }}</p>
                        <p class="text-xs text-slate-500">
                            @if($kunci === \App\Support\Semester::kunci($aktif)) <span class="font-bold text-emerald-700">Sedang berjalan</span>
                            @elseif(\App\Support\Semester::urut($s) > \App\Support\Semester::urut($aktif)) <span class="font-bold text-amber-700">Akan datang</span>
                            @else Sudah selesai @endif
                            @if(isset($custom[$kunci])) • ditambahkan admin @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($dipilih)<span class="text-xs font-bold text-teal-800 bg-teal-50 border border-teal-200 rounded-full px-3 py-1">Sedang dilihat</span>
                        @else
                        <form method="POST" action="{{ route('semester.pilih') }}">@csrf<input type="hidden" name="semester" value="{{ $kunci }}"><button class="btn-aksi btn-detail"><x-ikon nama="eye" class="w-3.5 h-3.5" />Lihat</button></form>
                        @endif
                        @if(isset($custom[$kunci]))
                        <form method="POST" action="{{ route('semester.destroy', $custom[$kunci]) }}" data-konfirmasi="Hapus semester ini dari daftar? Data di dalamnya tidak ikut terhapus.">@csrf @method('DELETE')<button class="btn-aksi btn-hapus"><x-ikon nama="trash" class="w-3.5 h-3.5" />Hapus</button></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="kartu bg-white rounded-2xl border-2 border-slate-200 p-5 h-fit">
        <h3 class="font-display text-lg text-ink-900 mb-3">Tambah Semester</h3>
        <form method="POST" action="{{ route('semester.berikutnya') }}" class="mb-4 pb-4 border-b-2 border-slate-100">@csrf
            <button class="tombol-aktif btn-utama w-full"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Semester Berikutnya</button>
            <p class="text-xs text-slate-500 mt-2">Satu klik menambah semester tepat setelah yang terbaru (mis. Genap 2026/2027, lalu Ganjil 2027/2028).</p>
        </form>
        <p class="text-sm font-semibold text-slate-700 mb-2">Atau isi manual:</p>
        <form method="POST" action="{{ route('semester.store') }}" class="space-y-3">
            @csrf
            <div><label class="text-sm font-semibold text-slate-700">Semester</label>
                <select name="semester" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none"><option value="Ganjil">Ganjil</option><option value="Genap">Genap</option></select></div>
            <div><label class="text-sm font-semibold text-slate-700">Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" placeholder="2027/2028" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('tahun_ajaran')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror</div>
            <button class="tombol-aktif btn-utama w-full"><x-ikon nama="plus" class="w-4 h-4" /> Tambah Semester</button>
        </form>
        <p class="text-xs text-slate-500 mt-4 leading-relaxed">Contoh: untuk tahun ajaran berikutnya isi <b>Ganjil</b> dengan <b>2027/2028</b>. Setelah ditambahkan, semester itu otomatis terpilih sehingga mapel, jadwal, tugas, dan materi baru langsung tercatat di semester tersebut.</p>
    </div>
</div>
@endsection
