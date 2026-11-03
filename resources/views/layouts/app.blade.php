<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, fotoModalUrl: null }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - EduVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.tema')
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cea3b4; border-radius: 10px; }
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
        .kartu::before {
            content: '';
            position: absolute; inset: -1.5px;
            border-radius: inherit;
            padding: 1.5px;
            background: conic-gradient(from var(--shimmer-angle, 0deg), transparent 0%, #d3b05a 8%, #a82a4f 16%, transparent 28%);
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            opacity: 0; transition: opacity .3s ease;
            z-index: -1;
            animation: putar-shimmer 2.5s linear infinite paused;
        }
        .kartu:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -12px rgba(15,23,42,.18); }
        .kartu:hover::before { opacity: 1; animation-play-state: running; }
        @property --shimmer-angle { syntax: '<angle>'; inherits: false; initial-value: 0deg; }
        @keyframes putar-shimmer { to { --shimmer-angle: 360deg; } }

        /* Tombol jadi sedikit terangkat saat hover, senada dengan kartu */
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); filter: brightness(1.05); }
    </style>
</head>
<body class="text-slate-800 min-h-screen overflow-x-hidden">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed z-40 inset-y-0 left-0 h-[100dvh] w-72 flex flex-col bg-gradient-to-b from-ink-700 via-ink-800 to-ink-900 text-slate-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-3 lg:m-3 lg:mr-0 lg:h-[calc(100vh-1.5rem)] lg:rounded-3xl lg:shrink-0 shadow-2xl shadow-ink-900/30 sidebar-glow">
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/25 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-200 to-brand-500 flex items-center justify-center font-display font-extrabold text-teal-900 text-lg shadow-lg shadow-ink-900/20 shrink-0">E</div>
                <div class="min-w-0">
                    <p class="font-display font-extrabold text-white text-lg leading-none tracking-tight truncate">EduVision</p>
                    <p class="text-[11px] text-brand-200 mt-0.5 truncate">Sistem Akademik Sekolah</p>
                </div>
            </div>

            <nav class="px-4 py-5 space-y-1 overflow-y-auto flex-1 min-h-0">
                @php
                    $user = auth()->user();
                    $menu = function($routeName) { return request()->routeIs($routeName) ? 'menu-aktif bg-brand-50 text-teal-700 border-transparent shadow-lg shadow-ink-900/20' : 'text-white/90 border-transparent hover:bg-white/15 hover:text-white'; };
                @endphp

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('dashboard') }} transition">
                    <span class="menu-ikon">🏠</span><span class="text-sm font-semibold">Dashboard</span>
                </a>

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-brand-300 font-bold">Master Data</p>
                {{-- Data Siswa & Mata Pelajaran: admin bisa kelola penuh, guru bisa LIHAT saja (dibatasi di route/controller) --}}
                <a href="{{ route('siswa.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('siswa.*') }} transition"><span class="menu-ikon">🎓</span><span class="text-sm font-semibold">Data Siswa</span></a>
                @if($user->isAdmin())
                <a href="{{ route('guru.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('guru.*') }} transition"><span class="menu-ikon">👨‍🏫</span><span class="text-sm font-semibold">Data Guru</span></a>
                <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('kelas.*') }} transition"><span class="menu-ikon">🏫</span><span class="text-sm font-semibold">Kelas & Wali</span></a>
                @endif
                <a href="{{ route('mapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('mapel.*') }} transition"><span class="menu-ikon">📘</span><span class="text-sm font-semibold">Mata Pelajaran</span></a>
                @endif

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-brand-300 font-bold">Akademik</p>
                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.*') }} transition"><span class="menu-ikon">🗓️</span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                @if($user->isGuru())
                {{-- Materi, Input Nilai, Pengumpulan Tugas, Bank Soal: KHUSUS guru/wali kelas, admin tidak punya akses lagi --}}
                <a href="{{ route('materi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.index') }} transition"><span class="menu-ikon">📚</span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('nilai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('nilai.*') }} transition"><span class="menu-ikon">📝</span><span class="text-sm font-semibold">Input Nilai</span></a>
                <a href="{{ route('tugas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.index') }} transition"><span class="menu-ikon">📥</span><span class="text-sm font-semibold">Pengumpulan Tugas</span></a>
                @endif
                <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.index') }} transition"><span class="menu-ikon">📷</span><span class="text-sm font-semibold">Rekap Absensi</span></a>
                @php $jumlahMenunggu = \App\Models\Absensi::where('status_persetujuan', 'menunggu')->count(); @endphp
                <a href="{{ route('absensi.persetujuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.persetujuan') }} transition"><span class="menu-ikon">⏳</span><span class="text-sm font-semibold flex-1">Persetujuan Absen</span>@if($jumlahMenunggu > 0)<span class="min-w-[1.5rem] text-center px-1.5 py-0.5 rounded-full bg-amber-400 text-ink-900 text-[11px] font-extrabold">{{ $jumlahMenunggu }}</span>@endif</a>
                <a href="{{ route('absensi.monitor') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.monitor') }} transition"><span class="menu-ikon">🛰️</span><span class="text-sm font-semibold">Monitor Lokasi & Wajah</span></a>
                {{-- Absen QR Per Mapel: KHUSUS guru/wali kelas, admin tidak lagi punya akses. --}}
                @if($user->isGuru())
                <a href="{{ route('absensimapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.index') }} transition"><span class="menu-ikon">📶</span><span class="text-sm font-semibold">Absen QR Per Mapel</span></a>
                @endif
                <a href="{{ route('absensimapel.bolos') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.bolos') }} transition"><span class="menu-ikon">🚩</span><span class="text-sm font-semibold">Deteksi Membolos</span></a>
                <a href="{{ route('prestasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('prestasi.*') }} transition"><span class="menu-ikon">🏆</span><span class="text-sm font-semibold">Prestasi Siswa</span></a>
                @if($user->isGuru())
                <a href="{{ route('banksoal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.index') }} transition"><span class="menu-ikon">❓</span><span class="text-sm font-semibold">Bank Soal</span></a>
                @endif
                <a href="{{ route('aspirasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.index') }} transition"><span class="menu-ikon">📢</span><span class="text-sm font-semibold">Kotak Aspirasi</span></a>
                @endif

                @if($user->isAdmin())
                <a href="{{ route('spp.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.index') }} transition"><span class="menu-ikon">💳</span><span class="text-sm font-semibold">SPP</span></a>
                <a href="{{ route('pengaturan.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('pengaturan.*') }} transition"><span class="menu-ikon">⚙️</span><span class="text-sm font-semibold">Pengaturan Sekolah</span></a>
                @endif

                @if($user->isSiswa())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-brand-300 font-bold">Menu Saya</p>
                <a href="{{ route('absensi.form') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.form') }} transition"><span class="menu-ikon">📷</span><span class="text-sm font-semibold">Absen Sekarang</span></a>
                <a href="{{ route('absensimapel.formManual') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.formManual') }} transition"><span class="menu-ikon">📶</span><span class="text-sm font-semibold">Absen QR Mapel</span></a>
                <a href="{{ route('jadwal.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.saya') }} transition"><span class="menu-ikon">🗓️</span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                <a href="{{ route('materi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.saya') }} transition"><span class="menu-ikon">📚</span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('tugas.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.saya') }} transition"><span class="menu-ikon">📝</span><span class="text-sm font-semibold">Tugas Saya</span></a>
                <a href="{{ route('rapor.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('rapor.saya') }} transition"><span class="menu-ikon">📄</span><span class="text-sm font-semibold">Rapor Saya</span></a>
                <a href="{{ route('spp.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.saya') }} transition"><span class="menu-ikon">💳</span><span class="text-sm font-semibold">Status SPP</span></a>
                <a href="{{ route('banksoal.latihan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.latihan') }} transition"><span class="menu-ikon">❓</span><span class="text-sm font-semibold">Latihan Soal</span></a>
                <a href="{{ route('aspirasi.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.create') }} transition"><span class="menu-ikon">📢</span><span class="text-sm font-semibold">Kirim Aspirasi</span></a>
                <a href="{{ route('aspirasi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.saya') }} transition"><span class="menu-ikon">📬</span><span class="text-sm font-semibold">Riwayat Aspirasi</span></a>
                @endif

                <div class="pt-4 mt-4 border-t border-white/25">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-3 px-4 py-2.5 rounded-xl w-full text-left text-white hover:bg-white/15 transition text-sm font-semibold">
                            <span>🚪</span><span>Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <button type="button" @click="sidebarOpen = true" x-show="!sidebarOpen" aria-label="Buka menu"
                class="lg:hidden fixed bottom-4 left-4 z-30 w-12 h-12 rounded-full bg-gradient-to-br from-teal-500 to-teal-700 text-white text-xl shadow-lg shadow-teal-700/40 flex items-center justify-center">☰</button>
        <div class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col min-w-0">
            <div class="mx-3 mt-3 rounded-3xl bg-white navbar-card relative z-20">
            <header class="text-ink-900 px-4 sm:px-6 pt-5 pb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 shrink-0 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-xl">☰</button>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-2xl font-extrabold text-ink-900 leading-snug break-words">@yield('title', 'Dashboard')</h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-medium leading-snug break-words">{{ now()->translatedFormat('l, d F Y') }} • EduVision</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-ink-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ str_replace('_',' ', auth()->user()->role) }}</p>
                    </div>
                    <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button @click="userMenu = !userMenu"
                                class="w-10 h-10 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center ring-2 ring-teal-200 hover:ring-brand-400 transition shrink-0 overflow-hidden">
                            @if(auth()->user()->fotoUrl())
                                {{-- Klik langsung pada foto membuka pop-up ukuran penuh (lihat modal
                                     global di bagian bawah halaman), bukan membuka menu dropdown. --}}
                                <img src="{{ auth()->user()->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                                     @click.stop="fotoModalUrl = '{{ auth()->user()->fotoUrl() }}'" alt="Foto profil">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </button>
                        <div x-show="userMenu" x-cloak x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border-2 border-slate-200 py-2 z-30">
                            <div class="px-4 py-2 border-b border-slate-50 sm:hidden">
                                <p class="text-sm font-semibold text-ink-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ str_replace('_',' ', auth()->user()->role) }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">🏠 Dashboard</a>
                            <a href="{{ route('akun.gantiPassword') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">🔒 Ganti Password</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">🚪 Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            </div>

            <main class="flex-1 p-4 sm:p-6">
                @if(session('sukses'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">✅ {{ session('sukses') }}</div>
                @endif
                @if(session('gagal'))
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium whitespace-pre-line">⚠️ {{ session('gagal') }}</div>
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

                @yield('content')
            </main>

            <footer class="mx-3 mb-3 rounded-3xl bg-gradient-to-r from-teal-800 to-teal-900 text-white/80 text-center text-xs py-4 px-4">
                <p>© {{ date('Y') }} <span class="font-display font-bold text-brand-300">EduVision</span> — Sistem Akademik Sekolah</p>
            </footer>
        </div>
    </div>

    {{-- Pop-up foto profil global: dipicu dari avatar di topbar maupun kartu
         "Halo" di dashboard (keduanya cukup set `fotoModalUrl`, karena x-data
         untuk ini ada di <html>, jadi bisa diakses dari mana saja di halaman). --}}
    <div x-show="fotoModalUrl" x-cloak @click.self="fotoModalUrl = null"
         class="fixed inset-0 bg-ink-900/70 z-[60] flex items-center justify-center p-4">
        <div class="relative max-w-sm w-full">
            <button type="button" @click="fotoModalUrl = null"
                    class="absolute -top-10 right-0 text-white/80 hover:text-white font-bold text-2xl">✕</button>
            <img :src="fotoModalUrl" class="w-full rounded-2xl shadow-2xl">
        </div>
    </div>
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
</body>
</html>
