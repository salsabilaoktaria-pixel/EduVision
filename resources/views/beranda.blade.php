<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — EduVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;}
        [x-cloak]{display:none!important;}
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); }
        .kartu { transition: transform .25s ease, box-shadow .25s ease; }
        .kartu:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -12px rgba(15,23,42,.18); }
        .gelombang path { stroke: none; }
    
/* ===== BERANDA: matching dark-luxury palette ===== */
body { background:#f3f0ff !important; color:#17152d !important; }
body > section:first-of-type, body > div:first-of-type > section:first-of-type { background:linear-gradient(120deg,#17264b 0%,#352064 58%,#51217a 100%) !important; color:#fff !important; }
body .bg-gradient-to-br.from-ink-900, body .from-ink-900.via-teal-900 { background-image:linear-gradient(125deg,#17264b 0%,#352064 58%,#51217a 100%) !important; }
body .from-teal-700, body .via-teal-900, body .to-teal-900 { --tw-gradient-from:#452078 var(--tw-gradient-from-position) !important; --tw-gradient-to:rgb(69 32 120 / 0) var(--tw-gradient-to-position) !important; --tw-gradient-stops:var(--tw-gradient-from),#25205a var(--tw-gradient-via-position),#17264b var(--tw-gradient-to-position) !important; }
body .bg-teal-600, body .bg-teal-700, body .hover\:bg-teal-700:hover { background:linear-gradient(100deg,#8b5cf6,#6d28d9) !important; color:#fff !important; }
body .text-teal-600, body .text-teal-700, body .text-teal-800 { color:#6d28d9 !important; }
body .text-teal-100, body .text-teal-200 { color:#e9ddff !important; }
body .bg-slate-50 { background:#f3f0ff !important; }
body .kartu { border:1px solid #ded4fb; box-shadow:0 12px 30px rgba(59,36,105,.10); }
body .kartu:nth-of-type(4n+1) { --tw-gradient-from:#8b5cf6 var(--tw-gradient-from-position); --tw-gradient-to:#6325c8 var(--tw-gradient-to-position); }
body .kartu:nth-of-type(4n+2) { --tw-gradient-from:#3b82f6 var(--tw-gradient-from-position); --tw-gradient-to:#4338ca var(--tw-gradient-to-position); }
body .kartu:nth-of-type(4n+3) { --tw-gradient-from:#f472b6 var(--tw-gradient-from-position); --tw-gradient-to:#be185d var(--tw-gradient-to-position); }


/* FINAL COLOR OVERRIDE — no green/teal in public pages */
body [class*="from-teal-"], body [class*="via-teal-"], body [class*="to-teal-"], body [class*="from-emerald-"], body [class*="to-emerald-"] { --tw-gradient-from:#452078 var(--tw-gradient-from-position) !important; --tw-gradient-to:#1D4ED8 var(--tw-gradient-to-position) !important; }
body [class*="bg-teal-"], body [class*="bg-emerald-"], body [class*="bg-green-"] { background-color:#6D28D9 !important; color:#fff !important; }
body [class*="text-teal-"], body [class*="text-emerald-"], body [class*="text-green-"] { color:#7C3AED !important; }
body [class*="border-teal-"], body [class*="border-emerald-"] { border-color:#C4B5FD !important; }


/* ===== FIX FINAL: remove remaining green from Beranda ===== */
body .tombol-aktif,
body a.tombol-aktif,
body .tombol-aktif:hover {
  background: linear-gradient(110deg, #8B5CF6 0%, #6366F1 52%, #3B82F6 100%) !important;
  background-image: linear-gradient(110deg, #8B5CF6 0%, #6366F1 52%, #3B82F6 100%) !important;
  color: #fff !important;
  box-shadow: 0 12px 28px rgba(99,102,241,.28) !important;
}
body [class*="to-teal-"], body [class*="via-teal-"], body [class*="from-teal-"] {
  --tw-gradient-from: #8B5CF6 var(--tw-gradient-from-position) !important;
  --tw-gradient-via: #6366F1 var(--tw-gradient-via-position) !important;
  --tw-gradient-to: #3B82F6 var(--tw-gradient-to-position) !important;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to) !important;
}
/* Logo badge: purple-to-blue instead of amber-to-green */
body header [class*="from-amber-400"][class*="to-teal-500"],
body header [class*="from-amber-400"][class*="to-teal-400"] {
  background: linear-gradient(135deg, #A78BFA, #3B82F6) !important;
  background-image: linear-gradient(135deg, #A78BFA, #3B82F6) !important;
  color: #fff !important;
}
/* Greeting highlight and any remaining teal accents */
body [class*="from-amber-300"][class*="to-teal-300"] {
  --tw-gradient-from: #F0ABFC var(--tw-gradient-from-position) !important;
  --tw-gradient-to: #60A5FA var(--tw-gradient-to-position) !important;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
}
body [class*="bg-teal-"], body [class*="bg-emerald-"], body [class*="bg-green-"] {
  background: linear-gradient(110deg, #8B5CF6, #3B82F6) !important;
  background-color: #6D28D9 !important;
  color: #fff !important;
}


/* LOGO EduVision */
.ev-logo { background:linear-gradient(135deg,#F472B6 0%,#8B5CF6 50%,#22D3EE 100%) !important; color:#fff !important; box-shadow:0 6px 20px rgba(139,92,246,.55), inset 0 1px 0 rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.25); text-shadow:0 1px 3px rgba(0,0,0,.35); }
</style>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            ink: { 900:'#2f3646', 800:'#3f4653', 700:'#535b66' },
            teal: { 50:'#f4f9f9',100:'#e1efec',200:'#c4ded9',300:'#9ec7c1',400:'#7aada4',500:'#50958b',600:'#437f76',700:'#3b6e67',800:'#38615a',900:'#325752' },
            indigo: { 50:'#f6f5fa',100:'#edebf4',200:'#dcd7ec',300:'#c4bcdc',400:'#aa9fcb',500:'#9384b8',600:'#8272a7',700:'#6b5b92',800:'#5f5080',900:'#544a70' },
            amber: { 50:'#f9f7f3',100:'#eee9dc',200:'#dbd0b6',300:'#c7b792',400:'#b6a279',500:'#a98f5d',600:'#957c4e',700:'#7f6944',800:'#705d3e',900:'#64543a' },
            rose: { 50:'#faf5f6',100:'#f5ecee',200:'#ead6db',300:'#d8b8c1',400:'#c29aa5',500:'#a77c89',600:'#955d6f',700:'#835061',800:'#744857',900:'#694351' },
        } } } }
    </script>
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
            ['emoji' => '🎓', 'judul' => 'Data Siswa', 'teks' => 'Kelola data & profil siswa', 'route' => 'siswa.index', 'warna' => 'teal'],
            ['emoji' => '👨‍🏫', 'judul' => 'Data Guru', 'teks' => 'Kelola data & profil guru', 'route' => 'guru.index', 'warna' => 'indigo'],
            ['emoji' => '🏫', 'judul' => 'Kelas & Wali', 'teks' => 'Atur kelas dan wali kelas', 'route' => 'kelas.index', 'warna' => 'amber'],
            ['emoji' => '📘', 'judul' => 'Mata Pelajaran', 'teks' => 'Kelola daftar mata pelajaran', 'route' => 'mapel.index', 'warna' => 'rose'],
            ['emoji' => '💳', 'judul' => 'SPP', 'teks' => 'Pantau status pembayaran SPP', 'route' => 'spp.index', 'warna' => 'teal'],
            ['emoji' => '🗓️', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat & atur jadwal per kelas', 'route' => 'jadwal.index', 'warna' => 'indigo'],
        ];
    } elseif ($user->isGuru()) {
        $pintasan = [
            ['emoji' => '📥', 'judul' => 'Pengumpulan Tugas', 'teks' => 'Beri tugas & nilai pengumpulan', 'route' => 'tugas.index', 'warna' => 'teal'],
            ['emoji' => '📝', 'judul' => 'Input Nilai', 'teks' => 'Kelola nilai siswa per mapel', 'route' => 'nilai.index', 'warna' => 'indigo'],
            ['emoji' => '📶', 'judul' => 'Absen QR Per Mapel', 'teks' => 'Buka sesi absen di kelas', 'route' => 'absensimapel.index', 'warna' => 'amber'],
            ['emoji' => '📚', 'judul' => 'Materi Pembelajaran', 'teks' => 'Bagikan materi ke siswa', 'route' => 'materi.index', 'warna' => 'rose'],
            ['emoji' => '🗓️', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat jadwal mengajarmu', 'route' => 'jadwal.index', 'warna' => 'teal'],
            ['emoji' => '❓', 'judul' => 'Bank Soal', 'teks' => 'Kelola soal latihan siswa', 'route' => 'banksoal.index', 'warna' => 'indigo'],
        ];
    } else {
        $pintasan = [
            ['emoji' => '📝', 'judul' => 'Tugas Saya', 'teks' => 'Lihat & kumpulkan tugas', 'route' => 'tugas.saya', 'warna' => 'teal'],
            ['emoji' => '🗓️', 'judul' => 'Jadwal Pelajaran', 'teks' => 'Lihat jadwal kelasmu', 'route' => 'jadwal.saya', 'warna' => 'indigo'],
            ['emoji' => '📄', 'judul' => 'Rapor Saya', 'teks' => 'Pantau nilai & rapor', 'route' => 'rapor.saya', 'warna' => 'amber'],
            ['emoji' => '💳', 'judul' => 'Status SPP', 'teks' => 'Cek status pembayaran SPP', 'route' => 'spp.saya', 'warna' => 'rose'],
            ['emoji' => '❓', 'judul' => 'Latihan Soal', 'teks' => 'Asah pemahamanmu kapan saja', 'route' => 'banksoal.latihan', 'warna' => 'teal'],
            ['emoji' => '📚', 'judul' => 'Materi Pembelajaran', 'teks' => 'Baca materi dari guru', 'route' => 'materi.saya', 'warna' => 'indigo'],
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
    <header class="relative overflow-hidden bg-gradient-to-br from-ink-900 via-teal-900 to-ink-900 text-white">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl ev-logo flex items-center justify-center font-extrabold text-lg">E</div>
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
            <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight">Halo, <span class="bg-gradient-to-r from-amber-300 to-teal-300 bg-clip-text text-transparent">{{ $namaTampil }}</span>!</h1>
            <p class="text-slate-300 mt-4 max-w-xl mx-auto">Senang kamu kembali di EduVision. Ini pintasan cepat ke fitur yang paling sering kamu pakai, atau langsung saja masuk ke dashboard lengkap.</p>
            <a href="{{ route('dashboard') }}" class="tombol-aktif inline-block mt-7 bg-gradient-to-r from-teal-500 to-teal-700 rounded-xl px-8 py-3.5 text-sm font-bold shadow-xl shadow-teal-900/40 hover:shadow-teal-900/60 transition">Buka Dashboard Lengkap →</a>
        </div>

        <svg class="gelombang absolute -bottom-px left-0 w-full" style="height:auto" viewBox="0 0 1440 90" fill="none" preserveAspectRatio="none">
            <path d="M0 48C240 90 480 8 720 28C960 48 1200 90 1440 48V91H0V48Z" fill="#f8fafc"/>
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
                    <div class="w-12 h-12 rounded-xl {{ $warnaKelas[$p['warna']] }} flex items-center justify-center text-2xl mb-3">{{ $p['emoji'] }}</div>
                    <h3 class="font-bold text-ink-900 mb-1">{{ $p['judul'] }}</h3>
                    <p class="text-sm text-slate-500">{{ $p['teks'] }}</p>
                </a>
            @endforeach
        </div>
    </main>

    <!-- RINGKASAN FITUR -->
    <section class="max-w-6xl mx-auto px-6 mt-20">
        <div class="rounded-3xl bg-gradient-to-br from-ink-900 via-teal-900 to-ink-900 text-white p-10 sm:p-14 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold max-w-xl mx-auto">Semua Fitur Ada di Dashboard</h2>
            <p class="text-slate-300 mt-3 max-w-md mx-auto">Absensi, tugas, nilai, rapor, bank soal, SPP, hingga kotak aspirasi — semuanya bisa diakses dari sana.</p>
            <a href="{{ route('dashboard') }}" class="tombol-aktif inline-block mt-7 bg-gradient-to-r from-amber-400 to-amber-600 text-ink-900 rounded-xl px-8 py-3.5 text-sm font-extrabold shadow-xl shadow-amber-900/30 hover:shadow-amber-900/50 transition">Buka Dashboard →</a>
        </div>
    </section>

    {{-- Pop-up foto profil ukuran penuh. --}}
    <div x-show="fotoModalUrl" x-cloak @click.self="fotoModalUrl = null"
         class="fixed inset-0 bg-ink-900/70 z-[60] flex items-center justify-center p-4">
        <div class="relative max-w-sm w-full">
            <button type="button" @click="fotoModalUrl = null"
                    class="absolute -top-10 right-0 text-white/80 hover:text-white font-bold text-2xl">✕</button>
            <img :src="fotoModalUrl" class="w-full rounded-2xl shadow-2xl">
        </div>
    </div>

    <footer class="mt-20 bg-gradient-to-r from-ink-900 via-teal-900 to-ink-900 text-slate-300 text-center text-xs py-5 px-4">
        <p>© {{ date('Y') }} <span class="font-bold text-white">EduVision</span> — Sistem Akademik Sekolah</p>
    </footer>
</body>
</html>
