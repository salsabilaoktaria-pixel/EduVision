<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — EduVision</title>
    @include('partials.tema-sekolah')
    <script defer src="{{ asset('vendor/alpine.min.js') }}"></script>
    <style>
        [x-cloak]{display:none!important;}
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); }
        .kartu { transition: transform .25s ease, box-shadow .25s ease; }
        .kartu:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -12px rgba(15,23,42,.18); }
        .gelombang path { stroke: none; }
    </style>
    @include('partials.css-tailwind')
</head>
<body class="bg-slate-50 text-slate-800" x-data="{ fotoModalUrl: null }">

@php
    $namaTampil = $user->siswa->nama ?? $user->guru->nama ?? $user->name;
    $jamSekarang = now()->hour;
    $sapaan = $jamSekarang < 11 ? 'Selamat Pagi' : ($jamSekarang < 15 ? 'Selamat Siang' : ($jamSekarang < 18 ? 'Selamat Sore' : 'Selamat Malam'));

    // Pintasan cepat, disesuaikan per peran — langsung menuju halaman yang
    // paling relevan, bukan cuma ke dashboard umum.
    if ($user->isAdmin()) {
        $pintasan = [
            ['ikon' => 'graduation', 'judul' => 'Data Siswa', 'teks' => 'Kelola data & profil siswa', 'route' => 'siswa.index', 'warna' => 'teal'],
            ['ikon' => 'presentation', 'judul' => 'Data Guru', 'teks' => 'Kelola data & profil guru', 'route' => 'guru.index', 'warna' => 'indigo'],
            ['ikon' => 'users', 'judul' => 'Kelas & Wali', 'teks' => 'Atur kelas dan wali kelas', 'route' => 'kelas.index', 'warna' => 'amber'],
            ['ikon' => 'book', 'judul' => 'Mata Pelajaran', 'teks' => 'Kelola daftar mata pelajaran', 'route' => 'mapel.index', 'warna' => 'rose'],
            ['ikon' => 'credit-card', 'judul' => 'SPP', 'teks' => 'Pantau status pembayaran SPP', 'route' => 'spp.index', 'warna' => 'teal'],
            ['ikon' => 'calendar', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat & atur jadwal per kelas', 'route' => 'jadwal.index', 'warna' => 'indigo'],
        ];
    } elseif ($user->isGuru()) {
        $pintasan = [
            ['ikon' => 'inbox', 'judul' => 'Pengumpulan Tugas', 'teks' => 'Beri tugas & nilai pengumpulan', 'route' => 'tugas.index', 'warna' => 'teal'],
            ['ikon' => 'edit', 'judul' => 'Input Nilai', 'teks' => 'Kelola nilai siswa per mapel', 'route' => 'nilai.index', 'warna' => 'indigo'],
            ['ikon' => 'qr-code', 'judul' => 'Absen QR Per Mapel', 'teks' => 'Buka sesi absen di kelas', 'route' => 'absensimapel.index', 'warna' => 'amber'],
            ['ikon' => 'book-open', 'judul' => 'Materi Pembelajaran', 'teks' => 'Bagikan materi ke siswa', 'route' => 'materi.index', 'warna' => 'rose'],
            ['ikon' => 'calendar', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat jadwal mengajarmu', 'route' => 'jadwal.index', 'warna' => 'teal'],
            ['ikon' => 'help', 'judul' => 'Bank Soal', 'teks' => 'Kelola soal latihan siswa', 'route' => 'banksoal.index', 'warna' => 'indigo'],
        ];
    } else {
        $pintasan = [
            ['ikon' => 'edit', 'judul' => 'Tugas Saya', 'teks' => 'Lihat & kumpulkan tugas', 'route' => 'tugas.saya', 'warna' => 'teal'],
            ['ikon' => 'calendar', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat jadwal kelasmu', 'route' => 'jadwal.saya', 'warna' => 'indigo'],
            ['ikon' => 'file-text', 'judul' => 'Rapor Saya', 'teks' => 'Pantau nilai & rapor', 'route' => 'rapor.saya', 'warna' => 'amber'],
            ['ikon' => 'credit-card', 'judul' => 'Status SPP', 'teks' => 'Cek status pembayaran SPP', 'route' => 'spp.saya', 'warna' => 'rose'],
            ['ikon' => 'help', 'judul' => 'Latihan Soal', 'teks' => 'Asah pemahamanmu kapan saja', 'route' => 'banksoal.latihan', 'warna' => 'teal'],
            ['ikon' => 'book-open', 'judul' => 'Materi Pembelajaran', 'teks' => 'Baca materi dari guru', 'route' => 'materi.saya', 'warna' => 'indigo'],
        ];
    }

    $warnaKelas = [
        'teal' => 'bg-teal-100 text-teal-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700',
    ];
@endphp

    <!-- HERO SAMBUTAN -->
    <header class="relative overflow-hidden hero-pastel text-white">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                <x-logo class="w-14 h-14" />
                <span class="text-xl font-extrabold tracking-tight">EduVision</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="tombol-aktif bg-white/10 border border-white/20 rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-white/20 transition">Keluar</button>
            </form>
        </div>

        <div class="max-w-6xl mx-auto px-6 pt-8 pb-28 text-center relative z-10">
            <div class="w-28 h-28 rounded-2xl bg-white/10 mx-auto mb-4 flex items-center justify-center text-3xl font-extrabold overflow-hidden">
                @if($user->fotoUrl())
                    {{-- Klik foto untuk melihat pop-up ukuran penuh. --}}
                    <img src="{{ $user->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                         @click="fotoModalUrl = '{{ $user->fotoUrl() }}'" alt="Foto profil">
                @else
                    {{ strtoupper(substr($namaTampil, 0, 1)) }}
                @endif
            </div>
            <span class="inline-block bg-white/10 text-teal-200 text-xs font-bold px-3 py-1.5 rounded-full mb-3">{{ $sapaan }}</span>
            <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight">Halo, <span class="bg-gradient-to-r from-teal-600 to-indigo-500 bg-clip-text text-transparent">{{ $namaTampil }}</span>!</h1>
            <p class="text-slate-300 mt-4 max-w-xl mx-auto">Senang kamu kembali di EduVision. Ini pintasan cepat ke fitur yang paling sering kamu pakai, atau langsung saja masuk ke dashboard lengkap.</p>
            <a href="{{ route('dashboard') }}" class="tombol-aktif btn-utama btn-besar mt-7">Buka Dashboard Lengkap <x-ikon nama="arrow-right" class="w-4 h-4" /></a>
        </div>

        <svg class="gelombang absolute -bottom-px left-0 w-full" style="height:auto" viewBox="0 0 1440 90" fill="none" preserveAspectRatio="none">
            <path d="M0 48C240 90 480 8 720 28C960 48 1200 90 1440 48V91H0V48Z" fill="#fffdf8"/>
        </svg>
    </header>

    <!-- PINTASAN CEPAT -->
    <main class="max-w-6xl mx-auto px-6 mt-14 pb-4">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <span class="text-teal-600 text-xs font-bold uppercase tracking-widest">Pintasan Cepat</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-2">Langsung ke yang Kamu Butuhkan</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($pintasan as $p)
                <a href="{{ route($p['route']) }}" class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200 block">
                    <div class="w-12 h-12 rounded-xl {{ $warnaKelas[$p['warna']] }} flex items-center justify-center mb-3"><x-ikon :nama="$p['ikon']" class="w-6 h-6" /></div>
                    <h3 class="font-bold text-ink-900 mb-1">{{ $p['judul'] }}</h3>
                    <p class="text-sm text-slate-500">{{ $p['teks'] }}</p>
                </a>
            @endforeach
        </div>
    </main>

    <!-- RINGKASAN FITUR -->
    <section class="max-w-6xl mx-auto px-6 mt-20">
        <div class="rounded-3xl hero-pastel text-white p-10 sm:p-14 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold max-w-xl mx-auto">Semua Fitur Ada di Dashboard</h2>
            <p class="text-slate-300 mt-3 max-w-md mx-auto">Absensi, tugas, nilai, rapor, bank soal, SPP, hingga kotak aspirasi — semuanya bisa diakses dari sana.</p>
            <a href="{{ route('dashboard') }}" class="tombol-aktif btn-utama btn-besar mt-7">Buka Dashboard Lengkap <x-ikon nama="arrow-right" class="w-4 h-4" /></a>
        </div>
    </section>

    {{-- Pop-up foto profil ukuran penuh. --}}
    <div x-show="fotoModalUrl" x-cloak @click.self="fotoModalUrl = null"
         class="fixed inset-0 bg-ink-900/70 z-[60] flex items-center justify-center p-4">
        <div class="relative max-w-sm w-full">
            <button type="button" @click="fotoModalUrl = null"
                    class="absolute -top-10 right-0 bg-white text-slate-800 rounded-full p-1.5 border-2 border-slate-300 font-bold text-2xl"><x-ikon nama="x" class="w-6 h-6" /></button>
            <img :src="fotoModalUrl" class="w-full rounded-2xl shadow-2xl">
        </div>
    </div>

    <footer class="mt-20 hero-pastel text-slate-300 text-center text-xs py-5 px-4">
        <p>© {{ date('Y') }} <span class="font-bold text-white">EduVision</span> — Sistem Akademik Sekolah</p>
    </footer>
</body>
</html>
