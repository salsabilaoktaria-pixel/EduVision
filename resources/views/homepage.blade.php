<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduVision — Sistem Akademik Sekolah</title>
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
        /* SVG gelombang: path digambar tanpa stroke sama sekali (hanya fill),
           dan sedikit "masuk" ke bawah (-1px) supaya tidak ada garis tipis/seam
           apa pun yang muncul di batas antara hero gelap dan konten terang. */
        .gelombang path { stroke: none; }
    
/* FINAL COLOR OVERRIDE — no green/teal in public pages */
body [class*="from-teal-"], body [class*="via-teal-"], body [class*="to-teal-"], body [class*="from-emerald-"], body [class*="to-emerald-"] { --tw-gradient-from:#452078 var(--tw-gradient-from-position) !important; --tw-gradient-to:#1D4ED8 var(--tw-gradient-to-position) !important; }
body [class*="bg-teal-"], body [class*="bg-emerald-"], body [class*="bg-green-"] { background-color:#6D28D9 !important; color:#fff !important; }
body [class*="text-teal-"], body [class*="text-emerald-"], body [class*="text-green-"] { color:#7C3AED !important; }
body [class*="border-teal-"], body [class*="border-emerald-"] { border-color:#C4B5FD !important; }


/* LOGO EduVision */
.ev-logo { background:linear-gradient(135deg,#F472B6 0%,#8B5CF6 50%,#22D3EE 100%) !important; color:#fff !important; box-shadow:0 6px 20px rgba(139,92,246,.55), inset 0 1px 0 rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.25); text-shadow:0 1px 3px rgba(0,0,0,.35); }
</style>
    <script>
        // Palet yang SAMA PERSIS dengan halaman login & dashboard, supaya warnanya konsisten di semua halaman.
        tailwind.config = { theme: { extend: { colors: {
            ink: { 900:'#2f3646', 800:'#3f4653', 700:'#535b66' },
            teal: { 50:'#f4f9f9',100:'#e1efec',200:'#c4ded9',300:'#9ec7c1',400:'#7aada4',500:'#50958b',600:'#437f76',700:'#3b6e67',800:'#38615a',900:'#325752' },
            indigo: { 50:'#f6f5fa',100:'#edebf4',200:'#dcd7ec',300:'#c4bcdc',400:'#aa9fcb',500:'#9384b8',600:'#8272a7',700:'#6b5b92',800:'#5f5080',900:'#544a70' },
            amber: { 50:'#f9f7f3',100:'#eee9dc',200:'#dbd0b6',300:'#c7b792',400:'#b6a279',500:'#a98f5d',600:'#957c4e',700:'#7f6944',800:'#705d3e',900:'#64543a' },
            rose: { 50:'#faf5f6',100:'#f5ecee',200:'#ead6db',300:'#d8b8c1',400:'#c29aa5',500:'#a77c89',600:'#955d6f',700:'#835061',800:'#744857',900:'#694351' },
        } } } }
    </script>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- HERO -->
    <header class="relative overflow-hidden bg-gradient-to-br from-ink-900 via-teal-900 to-ink-900 text-white">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl ev-logo flex items-center justify-center font-extrabold text-lg">E</div>
                <span class="text-xl font-extrabold tracking-tight">EduVision</span>
            </div>
            <a href="{{ route('login') }}" class="tombol-aktif bg-white text-ink-900 rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-50 transition">Masuk →</a>
        </div>

        <div class="max-w-6xl mx-auto px-6 pt-10 pb-28 text-center relative z-10">
            <span class="inline-block bg-white/10 text-teal-200 text-xs font-bold px-3 py-1.5 rounded-full mb-4">✨ Satu Platform, Seluruh Kegiatan Sekolah</span>
            <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight max-w-3xl mx-auto">Sistem Akademik Sekolah yang <span class="bg-gradient-to-r from-amber-300 to-teal-300 bg-clip-text text-transparent">Rapi, Cepat, & Transparan</span></h1>
            <p class="text-slate-300 mt-5 max-w-xl mx-auto">Absensi Face ID & QR per mapel, nilai, tugas, rapor, bank soal, SPP, hingga kotak aspirasi — semua terhubung dalam satu sistem.</p>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
                <a href="{{ route('login') }}" class="tombol-aktif inline-block bg-gradient-to-r from-teal-500 to-teal-700 rounded-xl px-8 py-3.5 text-sm font-bold shadow-xl shadow-teal-900/40 hover:shadow-teal-900/60 transition">Masuk ke Akun Saya</a>
                <a href="#fitur" class="tombol-aktif inline-block bg-white/10 border border-white/20 rounded-xl px-8 py-3.5 text-sm font-bold hover:bg-white/20 transition">Lihat Fitur ↓</a>
            </div>

            {{-- Statistik singkat, sekadar gambaran skala sistem --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-14 max-w-3xl mx-auto">
                <div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-300">8+</p>
                    <p class="text-xs text-slate-300 mt-1">Modul Akademik</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-300">3</p>
                    <p class="text-xs text-slate-300 mt-1">Peran Pengguna</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-300">24/7</p>
                    <p class="text-xs text-slate-300 mt-1">Akses Kapan Saja</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-300">1</p>
                    <p class="text-xs text-slate-300 mt-1">Platform Terpadu</p>
                </div>
            </div>
        </div>

        {{-- Lengkung dekoratif bergelombang (tetap dipertahankan sesuai permintaan) —
             dibuat tanpa stroke dan sedikit tumpang-tindih ke bawah supaya TIDAK ADA
             garis tipis/seam apa pun yang kelihatan di batas hero. --}}
        <svg class="gelombang absolute -bottom-px left-0 w-full" style="height:auto" viewBox="0 0 1440 90" fill="none" preserveAspectRatio="none">
            <path d="M0 48C240 90 480 8 720 28C960 48 1200 90 1440 48V91H0V48Z" fill="#f8fafc"/>
        </svg>
    </header>

    <!-- FITUR -->
    <main>
    <section id="fitur" class="max-w-6xl mx-auto px-6 mt-14 pb-4">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-teal-600 text-xs font-bold uppercase tracking-widest">Fitur Utama</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-2">Semua yang Sekolah Butuhkan, dalam Satu Tempat</h2>
            <p class="text-slate-500 mt-3">Dari absensi harian sampai rapor akhir semester, semuanya tercatat otomatis dan bisa dipantau real-time.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl mb-3">📷</div>
                <h3 class="font-bold text-ink-900 mb-1">Absensi Face ID + Lokasi</h3>
                <p class="text-sm text-slate-500">Absen masuk sekolah pakai kamera & GPS, otomatis tercatat sesuai zona waktu WIB.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl mb-3">📶</div>
                <h3 class="font-bold text-ink-900 mb-1">Absen QR Dinamis Per Mapel</h3>
                <p class="text-sm text-slate-500">QR berganti tiap beberapa detik, anti-screenshot, langsung terekap per jam pelajaran.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mb-3">📝</div>
                <h3 class="font-bold text-ink-900 mb-1">Tugas & Pengumpulan Online</h3>
                <p class="text-sm text-slate-500">Guru beri tugas lengkap dengan lampiran, siswa kumpulkan file/video/link, nilai otomatis masuk rapor.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl mb-3">❓</div>
                <h3 class="font-bold text-ink-900 mb-1">Bank Soal & Latihan</h3>
                <p class="text-sm text-slate-500">Upload soal dari Word/PDF/teks, siswa bisa latihan soal pilihan ganda kapan saja.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl mb-3">📊</div>
                <h3 class="font-bold text-ink-900 mb-1">Nilai & Rapor Otomatis</h3>
                <p class="text-sm text-slate-500">Input nilai per mapel, rapor otomatis terekap rapi per siswa dan per kelas, lengkap dengan grafik tren.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl mb-3">💳</div>
                <h3 class="font-bold text-ink-900 mb-1">SPP & Pembayaran</h3>
                <p class="text-sm text-slate-500">Status pembayaran SPP transparan per kelas, bisa dipantau kapan saja oleh siswa/orang tua.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mb-3">🗓️</div>
                <h3 class="font-bold text-ink-900 mb-1">Jadwal Pelajaran</h3>
                <p class="text-sm text-slate-500">Jadwal per kelas tersusun rapi, otomatis terhubung ke absen QR per mata pelajaran.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl mb-3">🏆</div>
                <h3 class="font-bold text-ink-900 mb-1">Prestasi Siswa</h3>
                <p class="text-sm text-slate-500">Catat pencapaian & prestasi siswa supaya terdokumentasi rapi sepanjang masa sekolah.</p>
            </div>
            <div class="kartu bg-white rounded-2xl p-6 shadow-sm border-2 border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl mb-3">📮</div>
                <h3 class="font-bold text-ink-900 mb-1">Kotak Aspirasi</h3>
                <p class="text-sm text-slate-500">Siswa bisa menyampaikan masukan langsung ke sekolah lewat satu tempat, direspons pihak sekolah.</p>
            </div>
        </div>
    </section>

    {{-- UNTUK SIAPA --}}
    <section class="max-w-6xl mx-auto px-6 mt-20">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-indigo-600 text-xs font-bold uppercase tracking-widest">Untuk Siapa</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-2">Satu Sistem, Tiga Peran</h2>
            <p class="text-slate-500 mt-3">Setiap peran mendapat tampilan dan akses yang relevan dengan kebutuhannya masing-masing.</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-5">
            <div class="kartu bg-gradient-to-br from-ink-900 to-teal-900 text-white rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl mb-3">🗂️</div>
                <h3 class="font-bold mb-1">Admin / Tata Usaha</h3>
                <p class="text-sm text-slate-300">Kelola data siswa, guru, kelas, mapel, SPP, dan pantau seluruh aktivitas sekolah dari satu dashboard.</p>
            </div>
            <div class="kartu bg-gradient-to-br from-teal-700 to-teal-900 text-white rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl mb-3">👨‍🏫</div>
                <h3 class="font-bold mb-1">Guru / Wali Kelas</h3>
                <p class="text-sm text-slate-300">Beri materi & tugas, nilai otomatis tersalur ke rapor, buka absen QR, dan pantau kelas binaan.</p>
            </div>
            <div class="kartu bg-gradient-to-br from-indigo-700 to-indigo-900 text-white rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl mb-3">🎓</div>
                <h3 class="font-bold mb-1">Siswa</h3>
                <p class="text-sm text-slate-300">Lihat jadwal, kumpulkan tugas, pantau nilai & rapor, latihan soal, hingga cek status SPP sendiri.</p>
            </div>
        </div>
    </section>

    {{-- CARA KERJA --}}
    <section class="max-w-6xl mx-auto px-6 mt-20">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-amber-600 text-xs font-bold uppercase tracking-widest">Cara Kerja</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-2">Mulai dalam 3 Langkah</h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-5">
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl font-extrabold mb-3 mx-auto">1</div>
                <h3 class="font-bold text-ink-900 mb-1">Masuk dengan Akun Sekolah</h3>
                <p class="text-sm text-slate-500">Gunakan akun yang diberikan admin — satu akun untuk semua fitur sesuai peranmu.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-extrabold mb-3 mx-auto">2</div>
                <h3 class="font-bold text-ink-900 mb-1">Jalankan Aktivitas Harian</h3>
                <p class="text-sm text-slate-500">Absen, beri/kumpulkan tugas, input nilai — semua tercatat otomatis, tanpa kertas.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-extrabold mb-3 mx-auto">3</div>
                <h3 class="font-bold text-ink-900 mb-1">Pantau Hasilnya Real-Time</h3>
                <p class="text-sm text-slate-500">Rapor, rekap kehadiran, dan status SPP langsung terlihat, kapan saja dibutuhkan.</p>
            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="max-w-6xl mx-auto px-6 mt-20">
        <div class="rounded-3xl bg-gradient-to-br from-ink-900 via-teal-900 to-ink-900 text-white p-10 sm:p-14 text-center relative overflow-hidden">
            <h2 class="text-2xl sm:text-3xl font-extrabold max-w-xl mx-auto">Siap Merapikan Administrasi Akademik Sekolahmu?</h2>
            <p class="text-slate-300 mt-3 max-w-md mx-auto">Masuk sekarang dan rasakan kemudahan mengelola kegiatan sekolah dalam satu platform.</p>
            <a href="{{ route('login') }}" class="tombol-aktif inline-block mt-7 bg-gradient-to-r from-amber-400 to-amber-600 text-ink-900 rounded-xl px-8 py-3.5 text-sm font-extrabold shadow-xl shadow-amber-900/30 hover:shadow-amber-900/50 transition">Masuk ke Akun Saya →</a>
        </div>
    </section>
    </main>

    <footer class="mt-20 bg-gradient-to-r from-ink-900 via-teal-900 to-ink-900 text-slate-300">
        <div class="max-w-6xl mx-auto px-6 py-10 grid sm:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-lg ev-logo flex items-center justify-center font-extrabold text-ink-900">E</div>
                    <span class="font-extrabold text-white text-lg">EduVision</span>
                </div>
                <p class="text-sm text-slate-400">Sistem akademik sekolah yang rapi, cepat, dan transparan — untuk admin, guru, dan siswa.</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Fitur</p>
                <ul class="text-sm space-y-2 text-slate-300">
                    <li>Absensi Face ID & QR per Mapel</li>
                    <li>Tugas, Nilai & Rapor</li>
                    <li>Bank Soal & Latihan</li>
                    <li>SPP & Kotak Aspirasi</li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Akses</p>
                <a href="{{ route('login') }}" class="tombol-aktif inline-block bg-white/10 border border-white/20 rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-white/20 transition">Masuk ke Akun Saya →</a>
            </div>
        </div>
        <div class="border-t border-white/10 text-center text-xs py-5 px-4">
            <p>© {{ date('Y') }} <span class="font-bold text-white">EduVision</span> — Sistem Akademik Sekolah</p>
        </div>
    </footer>
</body>
</html>
