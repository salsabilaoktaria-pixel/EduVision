@if(request()->headers->has('X-Modal'))
{{-- Mode pop-up: hanya isi halaman (tanpa sidebar/navbar), dimuat ke dalam jendela pop-up oleh partials/popup-aksi --}}
@php
    // Pesan sukses/gagal diteruskan ke halaman lengkap berikutnya, kecuali tujuannya memang halaman yang sedang ada di pop-up.
    $asalPopup = [request()->header('X-Modal-Asal'), request()->header('X-Modal-Sekarang')];
    if (!in_array(request()->fullUrl(), $asalPopup, true)) { foreach (['sukses', 'gagal'] as $kunciFlash) { if (session()->has($kunciFlash)) { session()->keep([$kunciFlash]); } } }
@endphp
@if($errors->any())
    <div class="popup-galat mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm"><ul class="list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div data-judul="@yield('title')">@yield('content')</div>
@else
<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, fotoModalUrl: null }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - EduVision</title>
    @include('partials.tema-sekolah')
    <script defer src="{{ asset('vendor/alpine.min.js') }}"></script>
    @if(request()->routeIs('dashboard'))<script src="{{ asset('vendor/chart.umd.min.js') }}"></script>@endif
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #aac3e6; border-radius: 10px; }
        [x-cloak] { display: none !important; }

        /* ===== Kartu "elegant lift + shimmer" =====
           Ditempel ke kartu-kartu di seluruh aplikasi (lihat kelas .kartu di bawah).
           Saat disentuh/hover: kartu terangkat halus + garis tepi memancarkan
           kilau (shimmer) berputar mengelilingi kartu — bukan popup sungguhan,
           cuma efek visual "terangkat" sesuai permintaan. */
        .kartu {
            position: relative;
            transition: transform .25s ease, box-shadow .25s ease;
            isolation: isolate;
        }
        /* Kilau melingkar yang menyambung: satu lapisan gradien kerucut diputar (rotate = dikerjakan GPU, jadi mulus),
           dipotong menjadi cincin tipis di tepi kartu. Elemen .kilau-ring dipasang otomatis oleh skrip di bawah. */
        .kartu::after { content: ''; position: absolute; left: 0; right: 0; bottom: -8px; height: 8px; } /* jembatan hover: kartu tidak "berkedip" saat terangkat */
        .kartu > .kilau-ring {
            position: absolute !important; inset: 0; margin: 0 !important; border: 0 !important; padding: 3px;
            display: block; border-radius: inherit; overflow: hidden; pointer-events: none; z-index: 3;
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            opacity: 0; transition: opacity .25s ease;
        }
        .kilau-ring > i {
            position: absolute; left: 50%; top: 50%; display: block;
            width: var(--kd, 700px); height: var(--kd, 700px); margin: calc(var(--kd, 700px) / -2) 0 0 calc(var(--kd, 700px) / -2);
            background: conic-gradient(from 0deg, transparent 0%, rgba(239,194,63,.2) 6%, #efc23f 26%, #5f86c4 40%, transparent 40.4%, transparent 100%);
            animation: putar-kilau 2.4s linear infinite paused; will-change: transform;
        }
        .kartu:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -12px rgba(15,23,42,.18); }
        .kartu:hover > .kilau-ring { opacity: 1; }
        .kartu:hover > .kilau-ring > i { animation-play-state: running; }
        @keyframes putar-kilau { to { transform: rotate(360deg); } }

        /* Tombol jadi sedikit terangkat saat hover, senada dengan kartu */
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); filter: brightness(1.05); }
    </style>
    @include('partials.css-tailwind')
</head>
<body class="text-slate-800 min-h-screen overflow-x-hidden">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed z-40 inset-y-0 left-0 h-[100dvh] w-72 flex flex-col text-ink-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-3 lg:m-3 lg:mr-0 lg:h-[calc(100vh-1.5rem)] lg:rounded-3xl lg:shrink-0 shadow-xl shadow-teal-600/10 sidebar-glow">
            <div class="flex items-center gap-3 px-6 py-6 border-b border-teal-200/70 shrink-0">
                @php $sekolahIni = \App\Models\Sekolah::saatIni(); @endphp
                @if($sekolahIni->logoUrl())
                    <img src="{{ $sekolahIni->logoUrl() }}" alt="Logo {{ $sekolahIni->nama }}" class="w-14 h-14 rounded-xl object-contain bg-white border border-teal-100 p-0.5 shrink-0">
                @else
                    <x-logo class="w-14 h-14" />
                @endif
                <div class="min-w-0">
                    <p class="font-display font-bold text-ink-900 {{ $sekolahIni->exists ? 'text-[15px] leading-tight line-clamp-2' : 'text-xl leading-none truncate' }}">{{ $sekolahIni->exists ? $sekolahIni->nama : 'EduVision' }}</p>
                    <p class="text-[11px] text-teal-600 mt-1 truncate">{{ $sekolahIni->exists ? 'Didukung EduVision' : 'Pemilik Platform' }}</p>
                </div>
            </div>

            <nav class="px-4 py-5 space-y-1 overflow-y-auto flex-1 min-h-0">
                @php
                    $user = auth()->user();
                    $menu = function($routeName) { return request()->routeIs($routeName) ? 'menu-aktif bg-white text-teal-700 border-teal-400 shadow-md shadow-teal-600/10' : 'text-ink-700 border-transparent hover:bg-white/70 hover:text-teal-700'; };
                @endphp
                @if($user->isSuperAdmin())
                <a href="{{ route('sekolah.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('sekolah.*') }} transition"><span class="menu-ikon"><x-ikon nama="school" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Kelola Sekolah</span></a>
                @else

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('dashboard') }} transition">
                    <span class="menu-ikon"><x-ikon nama="grid" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Dashboard</span>
                </a>
                @endif

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-teal-500 font-bold">Master Data</p>
                {{-- Data Siswa & Mata Pelajaran: admin bisa kelola penuh, guru bisa LIHAT saja (dibatasi di route/controller) --}}
                <a href="{{ route('siswa.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('siswa.*') }} transition"><span class="menu-ikon"><x-ikon nama="graduation" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Data Siswa</span></a>
                @if($user->isAdmin())
                <a href="{{ route('guru.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('guru.*') }} transition"><span class="menu-ikon"><x-ikon nama="presentation" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Data Guru</span></a>
                <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('kelas.*') }} transition"><span class="menu-ikon"><x-ikon nama="users" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Kelas & Wali</span></a>
                @endif
                <a href="{{ route('mapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('mapel.*') }} transition"><span class="menu-ikon"><x-ikon nama="book" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Mata Pelajaran</span></a>
                @endif

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-teal-500 font-bold">Akademik</p>
                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.*') }} transition"><span class="menu-ikon"><x-ikon nama="calendar" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                @if($user->isGuru())
                {{-- Materi, Input Nilai, Pengumpulan Tugas, Bank Soal: KHUSUS guru/wali kelas, admin tidak punya akses lagi --}}
                <a href="{{ route('materi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.index') }} transition"><span class="menu-ikon"><x-ikon nama="book-open" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('nilai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('nilai.*') }} transition"><span class="menu-ikon"><x-ikon nama="edit" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Input Nilai</span></a>
                <a href="{{ route('tugas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.index') }} transition"><span class="menu-ikon"><x-ikon nama="inbox" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Pengumpulan Tugas</span></a>
                @endif
                <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.index') }} transition"><span class="menu-ikon"><x-ikon nama="clipboard" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Rekap Absensi</span></a>
                @php $jumlahMenunggu = \App\Models\Absensi::where('status_persetujuan', 'menunggu')->count(); @endphp
                <a href="{{ route('absensi.persetujuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.persetujuan') }} transition"><span class="menu-ikon"><x-ikon nama="user-check" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold flex-1">Persetujuan Absen</span>@if($jumlahMenunggu > 0)<span class="min-w-[1.5rem] text-center px-1.5 py-0.5 rounded-full bg-brand-300 text-ink-900 text-[11px] font-extrabold">{{ $jumlahMenunggu }}</span>@endif</a>
                <a href="{{ route('absensi.monitor') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.monitor') }} transition"><span class="menu-ikon"><x-ikon nama="scan-face" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Monitor Lokasi & Wajah</span></a>
                {{-- Absen QR Per Mapel: KHUSUS guru/wali kelas, admin tidak lagi punya akses. --}}
                @if($user->isGuru())
                <a href="{{ route('absensimapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.index') }} transition"><span class="menu-ikon"><x-ikon nama="qr-code" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Absen QR Per Mapel</span></a>
                @endif
                <a href="{{ route('absensimapel.bolos') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.bolos') }} transition"><span class="menu-ikon"><x-ikon nama="flag" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Deteksi Membolos</span></a>
                <a href="{{ route('prestasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('prestasi.*') }} transition"><span class="menu-ikon"><x-ikon nama="award" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Prestasi Siswa</span></a>
                @if($user->isGuru())
                <a href="{{ route('banksoal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.index') }} transition"><span class="menu-ikon"><x-ikon nama="help" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Bank Soal</span></a>
                @endif
                <a href="{{ route('aspirasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.index') }} transition"><span class="menu-ikon"><x-ikon nama="megaphone" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Kotak Aspirasi</span></a>
                @endif

                @if($user->isAdmin())
                <a href="{{ route('kurikulum.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('kurikulum.*') }} transition"><span class="menu-ikon"><x-ikon nama="book-open" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Kurikulum</span></a>
                <a href="{{ route('semester.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('semester.index') }} transition"><span class="menu-ikon"><x-ikon nama="calendar" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Semester</span></a>
                <a href="{{ route('spp.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.index') }} transition"><span class="menu-ikon"><x-ikon nama="credit-card" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">SPP</span></a>
                <a href="{{ route('pengaturan.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('pengaturan.*') }} transition"><span class="menu-ikon"><x-ikon nama="settings" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Pengaturan Sekolah</span></a>
                @endif

                @if($user->isSiswa())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-teal-500 font-bold">Menu Saya</p>
                <a href="{{ route('absensi.form') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.form') }} transition"><span class="menu-ikon"><x-ikon nama="scan-face" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Absen Sekarang</span></a>
                <a href="{{ route('absensimapel.formManual') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.formManual') }} transition"><span class="menu-ikon"><x-ikon nama="qr-code" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Absen QR Mapel</span></a>
                <a href="{{ route('jadwal.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.saya') }} transition"><span class="menu-ikon"><x-ikon nama="calendar" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                <a href="{{ route('materi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.saya') }} transition"><span class="menu-ikon"><x-ikon nama="book-open" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('tugas.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.saya') }} transition"><span class="menu-ikon"><x-ikon nama="edit" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Tugas Saya</span></a>
                <a href="{{ route('rapor.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('rapor.saya') }} transition"><span class="menu-ikon"><x-ikon nama="file-text" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Rapor Saya</span></a>
                <a href="{{ route('spp.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.saya') }} transition"><span class="menu-ikon"><x-ikon nama="credit-card" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Status SPP</span></a>
                <a href="{{ route('banksoal.latihan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.latihan') }} transition"><span class="menu-ikon"><x-ikon nama="help" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Latihan Soal</span></a>
                <a href="{{ route('aspirasi.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.create') }} transition"><span class="menu-ikon"><x-ikon nama="send" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Kirim Aspirasi</span></a>
                <a href="{{ route('aspirasi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.saya') }} transition"><span class="menu-ikon"><x-ikon nama="file-text" class="w-[18px] h-[18px]" /></span><span class="text-sm font-semibold">Riwayat Aspirasi</span></a>
                @endif

                <div class="pt-4 mt-4 border-t border-teal-200/70">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-3 px-4 py-2.5 rounded-xl w-full text-left text-ink-700 hover:bg-white/70 hover:text-rose-600 transition text-sm font-semibold">
                            <span class="menu-ikon"><x-ikon nama="log-out" class="w-[18px] h-[18px]" /></span><span>Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <button type="button" @click="sidebarOpen = true" x-show="!sidebarOpen" aria-label="Buka menu"
                class="lg:hidden fixed bottom-4 left-4 z-30 w-12 h-12 rounded-full bg-teal-500 text-white shadow-lg shadow-teal-500/30 flex items-center justify-center"><x-ikon nama="menu" class="w-6 h-6" /></button>
        <div class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col min-w-0">
            <div class="mx-3 mt-3 rounded-3xl bg-white navbar-card relative z-20">
            <header class="text-ink-900 px-4 sm:px-6 pt-5 pb-4 flex flex-nowrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 shrink-0 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center"><x-ikon nama="menu" class="w-5 h-5" /></button>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-2xl font-extrabold text-ink-900 leading-snug break-words">@yield('title', 'Dashboard')</h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-medium leading-snug break-words">{{ now()->translatedFormat('l, d F Y') }} • {{ $sekolahIni->exists ? $sekolahIni->nama : 'EduVision' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 shrink-0 ml-auto">
                    @php $semTerpilih = \App\Support\Semester::terpilih(); @endphp
                    @php
                        $jumlahNotif = \App\Models\Notifikasi::where('user_id', auth()->id())->belumDibaca()->count();
                        $notifTerbaru = \App\Models\Notifikasi::where('user_id', auth()->id())->orderByDesc('created_at')->limit(6)->get();
                    @endphp
                    <div class="text-right hidden sm:flex flex-col items-end min-w-0">
                        <p class="text-sm font-semibold text-ink-900 leading-tight truncate max-w-[16rem]">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize leading-tight">{{ str_replace('_',' ', auth()->user()->role) }}</p>
                        <form method="POST" action="{{ route('semester.pilih') }}" class="mt-1">
                            @csrf
                            <label class="sr-only" for="pilih-semester">Semester</label>
                            <select id="pilih-semester" name="semester" onchange="this.form.submit()" class="rounded-full border-2 border-teal-200 bg-teal-50 text-teal-800 text-[11px] font-bold pl-2.5 pr-1 py-0.5 outline-none cursor-pointer max-w-[13rem]">
                                @foreach(\App\Support\Semester::opsi() as $so)
                                    <option value="{{ \App\Support\Semester::kunci($so) }}" @selected(\App\Support\Semester::kunci($so) === \App\Support\Semester::kunci($semTerpilih))>{{ \App\Support\Semester::label($so) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button @click="userMenu = !userMenu"
                                class="w-11 h-11 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center ring-2 ring-teal-200 hover:ring-brand-300 transition shrink-0 overflow-hidden">
                            @if(auth()->user()->fotoUrl())
                                {{-- Klik foto = membuka menu profil (sama untuk semua peran). Foto ukuran penuh: menu "Lihat Foto". --}}
                                <img src="{{ auth()->user()->fotoUrl() }}" class="w-full h-full object-cover" alt="Foto profil">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </button>
                        <div x-show="userMenu" x-cloak x-transition
                             class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border-2 border-slate-200 py-2 z-30">
                            <div class="px-4 py-2 border-b border-slate-50 sm:hidden">
                                <p class="text-sm font-semibold text-ink-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ str_replace('_',' ', auth()->user()->role) }}</p>
                            <form method="POST" action="{{ route('semester.pilih') }}" class="mt-1.5">@csrf
                                    <select name="semester" onchange="this.form.submit()" aria-label="Semester" class="w-full rounded-full border-2 border-teal-200 bg-teal-50 text-teal-800 text-[11px] font-bold px-2.5 py-1 outline-none">
                                        @foreach(\App\Support\Semester::opsi() as $so)<option value="{{ \App\Support\Semester::kunci($so) }}" @selected(\App\Support\Semester::kunci($so) === \App\Support\Semester::kunci($semTerpilih))>{{ \App\Support\Semester::label($so) }}</option>@endforeach
                                    </select>
                                </form>
                            </div>
                            <a href="{{ route('profil') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"><x-ikon nama="user" class="w-4 h-4 inline-block align-[-2px] mr-1.5" />Profil Saya</a>
                            @if(auth()->user()->fotoUrl())
                            <button type="button" @click="fotoModalUrl = '{{ auth()->user()->fotoUrl() }}'; userMenu = false" class="block w-full text-left px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"><x-ikon nama="eye" class="w-4 h-4 inline-block align-[-2px] mr-1.5" />Lihat Foto</button>
                            @endif
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"><x-ikon nama="grid" class="w-4 h-4 inline-block align-[-2px] mr-1.5" />Dashboard</a>
                            <a href="{{ route('akun.gantiPassword') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"><x-ikon nama="lock" class="w-4 h-4 inline-block align-[-2px] mr-1.5" />Ganti Password</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50"><x-ikon nama="log-out" class="w-4 h-4 inline-block align-[-2px] mr-1.5" />Keluar</button>
                            </form>
                        </div>
                    </div>
                    <div class="relative" x-data="{ notif: false }" @click.outside="notif = false" @keydown.escape.window="notif = false">
                        <button type="button" @click="notif = !notif" aria-label="Notifikasi"
                                class="relative w-11 h-11 rounded-full bg-teal-50 text-teal-700 hover:bg-teal-100 transition flex items-center justify-center">
                            <x-ikon nama="bell" class="w-5 h-5" />
                            @if($jumlahNotif > 0)
                                <span class="absolute -top-1 -right-1 min-w-[1.15rem] h-[1.15rem] px-1 rounded-full bg-brand-400 text-ink-900 text-[10px] font-extrabold flex items-center justify-center ring-2 ring-white">{{ $jumlahNotif > 9 ? '9+' : $jumlahNotif }}</span>
                            @endif
                        </button>
                        <div x-show="notif" x-cloak x-transition
                             class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-xl border border-slate-200 z-30 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <p class="text-sm font-bold text-ink-900">Notifikasi</p>
                                @if($jumlahNotif > 0)
                                <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">@csrf
                                    <button class="text-[11px] font-semibold text-teal-700 hover:underline">Tandai dibaca</button>
                                </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                @forelse($notifTerbaru as $n)
                                    <a href="{{ route('notifikasi.baca', $n) }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 {{ $n->dibaca_pada ? '' : 'bg-teal-50/60' }}">
                                        <span class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center {{ $n->dibaca_pada ? 'bg-slate-100 text-slate-500' : 'bg-teal-100 text-teal-700' }}"><x-ikon :nama="$n->namaIkon()" class="w-4 h-4" /></span>
                                        <span class="min-w-0">
                                            <span class="block text-xs font-bold text-ink-900 leading-snug">{{ $n->judul }}</span>
                                            @if($n->isi)<span class="block text-[11px] text-slate-500 leading-snug line-clamp-2">{{ $n->isi }}</span>@endif
                                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <p class="px-4 py-8 text-center text-xs text-slate-400">Belum ada notifikasi.</p>
                                @endforelse
                            </div>
                            <a href="{{ route('notifikasi.index') }}" class="block text-center text-xs font-bold text-teal-700 py-2.5 border-t border-slate-100 hover:bg-slate-50">Lihat semua</a>
                        </div>
                    </div>
                </div>
            </header>
            </div>

            <main class="flex-1 p-4 sm:p-6">
                @if(session('sukses'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><x-ikon nama="check-circle" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ session('sukses') }}</div>
                @endif
                @if(session('gagal'))
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium whitespace-pre-line"><x-ikon nama="alert" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ session('gagal') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Judul halaman + tanggal sekarang digabung ke header atas (lihat <h1> di atas),
                     jadi tidak ada lagi kartu judul terpisah di sini. --}}

                @if(request()->routeIs('nilai.*','tugas.*','materi.*','jadwal.*','spp.*','prestasi.*','aspirasi.*','rapor.*','tugas.saya','materi.saya','absensi.index','absensi.monitor','dashboard','jadwal.saya','spp.saya'))
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border-2 border-teal-200 bg-teal-50 px-4 py-1.5 text-xs font-bold text-teal-800">
                        <x-ikon nama="calendar" class="w-4 h-4" /> Data ditampilkan: {{ \App\Support\Semester::label(\App\Support\Semester::terpilih()) }}
                        <span class="font-semibold text-teal-700/80">(ganti lewat pilihan semester di bagian atas)</span>
                    </div>
                @endif
                @yield('content')
            </main>

            <footer class="mx-3 mb-3 rounded-3xl bg-white/70 border border-teal-100 text-slate-500 text-center text-xs py-4 px-4">
                <p>© {{ date('Y') }} <span class="font-display font-bold text-teal-600">EduVision</span> — Sistem Akademik Sekolah</p>
            </footer>
        </div>
    </div>

    {{-- Pop-up foto profil global: dipicu dari avatar di topbar maupun kartu
         "Halo" di dashboard (keduanya cukup set `fotoModalUrl`, karena x-data
         untuk ini ada di <html>, jadi bisa diakses dari mana saja di halaman). --}}
    <div x-show="fotoModalUrl" x-cloak @click.self="fotoModalUrl = null"
         class="fixed inset-0 bg-ink-900/60 z-[60] flex items-center justify-center p-4">
        <div class="relative max-w-sm w-full">
            <button type="button" @click="fotoModalUrl = null"
                    class="absolute -top-10 right-0 bg-white text-slate-800 rounded-full p-1.5 border-2 border-slate-300 font-bold"><x-ikon nama="x" class="w-6 h-6" /></button>
            <img :src="fotoModalUrl" class="w-full rounded-2xl shadow-2xl">
        </div>
    </div>
    <script>
        // Pasang lapisan kilau ke setiap kartu (termasuk kartu yang muncul belakangan lewat pop-up / pembaruan halaman).
        (function () {
            function pasang(root) {
                (root || document).querySelectorAll('.kartu:not([data-kilau])').forEach(function (k) {
                    k.dataset.kilau = '1';
                    var r = document.createElement('span'); r.className = 'kilau-ring'; r.setAttribute('aria-hidden', 'true');
                    r.appendChild(document.createElement('i')); k.appendChild(r);
                });
            }
            pasang();
            var menunggu = false;
            new MutationObserver(function () {
                if (menunggu) return; menunggu = true;
                requestAnimationFrame(function () { menunggu = false; pasang(); });
            }).observe(document.body, { childList: true, subtree: true });
            // Ukuran lapisan putar disesuaikan dengan diagonal kartu supaya cincin selalu tertutup penuh
            document.addEventListener('mouseover', function (e) {
                var k = e.target.closest ? e.target.closest('.kartu') : null;
                if (!k) return;
                var d = Math.ceil(Math.hypot(k.offsetWidth, k.offsetHeight)) + 8;
                if (k.dataset.kd !== String(d)) { k.dataset.kd = d; k.style.setProperty('--kd', d + 'px'); }
            }, { passive: true });
        })();
    </script>
    <script>
        // Tabel lebar otomatis dibungkus area scroll horizontal, supaya saat jendela dikecilkan
        // tabelnya bergeser di dalam kartu dan tidak merusak tata letak seluruh halaman.
        document.querySelectorAll('main table').forEach(t => {
            if (t.closest('.overflow-x-auto')) return;
            const w = document.createElement('div');
            w.className = 'overflow-x-auto max-w-full';
            t.parentNode.insertBefore(w, t);
            w.appendChild(t);
        });
    </script>
@include('partials.dialog')
    @include('partials.notif-suara')
    @include('partials.popup-aksi')
</body>
</html>
@endif
