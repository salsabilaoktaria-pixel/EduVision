{{-- ============================================================================
     TEMA TUNGGAL EduVision — dipakai SEMUA halaman (app, login, homepage, beranda)
     supaya warna & font konsisten dan cukup diubah di SATU tempat.

<<<<<<< HEAD
     Arah desain: warna MENYALA & BERANI (bukan hijau yang kusam).
       - Ruby       = warna utama (kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                      jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah).
       - Emas       = aksen mewah ("brand"), Mauve = sekunder ("indigo"), Koral = bahaya ("rose").
       - Wine-black = teks judul ("ink"). Latar halaman = gading (ivory).
       Berani karena ruby & emas pekat, elegan karena nadanya tertahan (tidak neon) dan gradien
       hanya bermain di SATU keluarga warna — nyaman di mata, tidak menyilaukan.
       Sidebar = ruby pekat; navbar = putih gading → keduanya jelas terpisah.
=======
     Arah desain: PASTEL RINGAN bernuansa sekolah — lembut di mata, tetap rapi & berkarakter.
       - Iris (lilac)   = warna utama. Kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                          jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah.
       - Peach          = aksen hangat ("brand"), Sakura = sekunder ("indigo"),
         Butter = peringatan ("amber"), Koral lembut = bahaya ("rose"), Mint = sukses ("emerald").
       - Netral ("slate") bernada lavender-abu, teks judul ("ink") ungu tua kebiruan agar tetap terbaca.
       Sidebar = lilac muda; navbar = putih; footer = lilac sangat muda → tiga panel terpisah jelas.
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
     Font: Bricolage Grotesque (judul, berkarakter) + DM Sans (isi, mudah dibaca).
     ============================================================================ --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', system-ui, sans-serif; }
<<<<<<< HEAD
    body { background-color: #fbf7f2; overflow-x: hidden;
        background-image: radial-gradient(900px 520px at 100% -8%, rgba(196,61,99,.06), transparent 60%),
                          radial-gradient(760px 460px at 0% 108%, rgba(193,154,63,.09), transparent 60%);
        background-attachment: fixed; }
    /* Sidebar: burgundy pekat dengan cahaya jingga di sudut — satu-satunya area "gelap & menyala" */
    .sidebar-glow { background-image: radial-gradient(380px 300px at 0% 0%, rgba(255,255,255,.14), transparent 62%),
                                      radial-gradient(340px 280px at 100% 100%, rgba(211,176,90,.30), transparent 62%),
                                      linear-gradient(170deg, #8a2142 0%, #701d38 55%, #5a1a31 100%) !important; }
    /* Navbar: lengkung sempurna — garis emas di atas dibuat dengan inset-shadow sehingga
       MENGIKUTI lengkung sudut kartu persis (tidak terpotong/bergerigi) */
    .navbar-card { border: 1px solid #efe3dc; box-shadow: inset 0 4px 0 0 #d3b05a, 0 10px 30px -14px rgba(90,26,49,.30); }
    .menu-ikon { width: 2rem; height: 2rem; border-radius: .6rem; display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.12); font-size: .95rem; flex-shrink: 0; transition: background .2s ease; }
    a:hover > .menu-ikon { background: rgba(255,255,255,.22); }
    .menu-aktif .menu-ikon { background: rgba(168,42,79,.12); }
    /* Kartu berwarna (gradien): kilau lembut di sudut kanan atas, di bawah teks */
    .kartu[class*="bg-gradient"]::after { content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; z-index: -1;
        background: radial-gradient(circle at 100% 0%, rgba(255,255,255,.30), transparent 46%); }
    /* Semua kotak putih di halaman app memakai warna garis tepi YANG SAMA (tidak beda-beda per halaman) */
    main .bg-white[class*="rounded"][class*="border"] { border-color: #f0e0d6 !important; }
    /* Semua kartu berwarna: TANPA garis tepi (seragam), hanya kilau & bayangan */
    .kartu[class*="bg-gradient"] { border: 0 !important; }
    tbody tr:hover { background-color: #fbf4f1 !important; }
    img, video, canvas, svg { max-width: 100%; }
    /* Kartu putih: garis tipis hangat + bayangan lembut berlapis (bukan border tebal abu-abu) */
    .kartu.bg-white { border-width: 1px !important; border-color: #f0e0d6 !important; border-radius: 1.25rem !important;
        box-shadow: 0 1px 2px rgba(90,26,49,.05), 0 12px 28px -14px rgba(90,26,49,.16); }
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { border-color: #e2cbbd !important; background-color: #fffefd; }
    input:focus, select:focus, textarea:focus { border-color: #c43d63 !important; box-shadow: 0 0 0 3px rgba(196,61,99,.16) !important; }
    .border-2 { border-width: 1.5px; }
    thead tr { background: #fbf3f5; }
    thead th { padding-left: .75rem; color: #7a4a5a !important; }
=======
    body { background-color: #faf8ff; overflow-x: hidden;
        background-image: radial-gradient(900px 520px at 100% -8%, rgba(167,152,228,.14), transparent 60%),
                          radial-gradient(760px 460px at 0% 108%, rgba(251,189,146,.16), transparent 60%);
        background-attachment: fixed; }
    /* Sidebar: lilac muda, teks gelap — terpisah dari navbar (putih) dan footer */
    .sidebar-glow { background-image: radial-gradient(360px 280px at 0% 0%, rgba(255,255,255,.75), transparent 62%),
                                      radial-gradient(320px 260px at 100% 100%, rgba(251,189,146,.30), transparent 62%),
                                      linear-gradient(170deg, #ece6fb 0%, #f1eafa 55%, #f9e9f1 100%) !important;
                    border: 1px solid #e3dcf5; }
    /* Navbar: lengkung sempurna — garis peach di atas dibuat dengan inset-shadow sehingga
       MENGIKUTI lengkung sudut kartu persis (tidak terpotong/bergerigi) */
    .navbar-card { border: 1px solid #ebe6f5; box-shadow: inset 0 4px 0 0 #f7c9a8, 0 10px 30px -16px rgba(95,81,166,.25); }
    .menu-ikon { width: 2rem; height: 2rem; border-radius: .6rem; display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.65); color: #7767c4; flex-shrink: 0; transition: background .2s ease; }
    a:hover > .menu-ikon { background: #fff; }
    .menu-aktif .menu-ikon { background: #ece8fb; color: #5f51a6; }
    /* Kartu berwarna: gradien PASTEL dengan teks gelap (bukan putih), tanpa garis tepi */
    .kartu[class*="bg-gradient"] { border: 0 !important; color: #3a3157 !important; box-shadow: 0 10px 26px -16px rgba(95,81,166,.35) !important; }
    .kartu[class*="bg-gradient"] * { color: inherit !important; }
    .kartu[class*="bg-gradient"] [class*="bg-white/"] { background: rgba(255,255,255,.62) !important; }
    .kartu[class*="bg-gradient"][class*="from-teal"]    { background-image: linear-gradient(135deg,#e6e0fb,#d2c8f5) !important; }
    .kartu[class*="bg-gradient"][class*="from-indigo"]  { background-image: linear-gradient(135deg,#fde4ee,#f9ccdf) !important; color:#5f2a47 !important; }
    .kartu[class*="bg-gradient"][class*="from-brand"]   { background-image: linear-gradient(135deg,#ffe9d8,#fdd2b4) !important; color:#6a3a25 !important; }
    .kartu[class*="bg-gradient"][class*="from-rose"]    { background-image: linear-gradient(135deg,#ffe1dc,#fbc8bf) !important; color:#70332e !important; }
    .kartu[class*="bg-gradient"][class*="from-emerald"] { background-image: linear-gradient(135deg,#daf3e6,#bfe6d3) !important; color:#214939 !important; }
    .kartu[class*="bg-gradient"][class*="from-amber"]   { background-image: linear-gradient(135deg,#fff3c9,#fde79b) !important; color:#5a4015 !important; }
    /* Hero / panel besar bernuansa pastel (dipakai di beranda, homepage, login) */
    .hero-pastel { background-image: linear-gradient(135deg,#ece6fb 0%,#f6e8f3 55%,#fdebdd 100%); color: #3a3157; }
    .hero-pastel :is(.text-white,.text-slate-300,.text-slate-400,.text-teal-100,.text-teal-200):not([class*="bg-gradient"]) { color: #4d4570 !important; }
    .hero-pastel [class*="bg-white/10"] { background: rgba(255,255,255,.6) !important; }
    .hero-pastel [class*="border-white/"] { border-color: rgba(119,103,196,.22) !important; }
    /* Semua kotak putih di halaman app memakai warna garis tepi YANG SAMA */
    main .bg-white[class*="rounded"][class*="border"] { border-color: #ebe6f5 !important; }
    tbody tr:hover { background-color: #f8f6fe !important; }
    img, video, canvas, svg { max-width: 100%; }
    /* Kartu putih: garis tipis + bayangan lembut berlapis */
    .kartu.bg-white { border-width: 1px !important; border-color: #ebe6f5 !important; border-radius: 1.25rem !important;
        box-shadow: 0 1px 2px rgba(70,55,120,.05), 0 12px 28px -16px rgba(70,55,120,.16); }
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { border-color: #d9d2ea !important; background-color: #fff; }
    input:focus, select:focus, textarea:focus { border-color: #8c7bd6 !important; box-shadow: 0 0 0 3px rgba(140,123,214,.18) !important; }
    .border-2 { border-width: 1.5px; }
    thead tr { background: #f6f3fd; }
    thead th { padding-left: .75rem; color: #6a5f8a !important; }
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    /* Responsif: pada layar sempit, kolom form bertumpuk, form pencarian boleh turun baris,
       dan input berlebar tetap tidak melebihi layar. */
    .w-64 { max-width: 100%; }
    main form.flex { flex-wrap: wrap; }
    @media (max-width: 420px) { main form .grid.grid-cols-2 { grid-template-columns: 1fr; } }
    h1, h2, h3, .font-display { font-family: 'Bricolage Grotesque', 'DM Sans', system-ui, sans-serif; letter-spacing: -0.015em; }
</style>
<script>
    tailwind.config = { theme: { extend: {
        fontFamily: {
            sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
            display: ['"Bricolage Grotesque"', '"DM Sans"', 'sans-serif'],
        },
        colors: {
<<<<<<< HEAD
            teal: { // UTAMA — ruby (merah anggur) yang kaya & elegan
                50:'#fdf3f5',100:'#fbe3e8',200:'#f6c6d1',300:'#ec9aae',400:'#dd6684',
                500:'#c43d63',600:'#a82a4f',700:'#8a2142',800:'#701d38',900:'#5a1a31',
            },
            brand: { // AKSEN — emas champagne (mewah, tidak menyilaukan)
                50:'#fbf7ec',100:'#f5ecd0',200:'#ecdba3',300:'#e0c677',400:'#d3b05a',
                500:'#c19a3f',600:'#a37e2f',700:'#816027',800:'#6a4f26',900:'#594223',
            },
            indigo: { // SEKUNDER / info — mauve plum yang lembut
                50:'#f8f3f5',100:'#f0e4e9',200:'#e1c9d3',300:'#cea3b4',400:'#b5788f',
                500:'#8e4a68',600:'#763a55',700:'#5f2f45',800:'#4a2536',900:'#3a1d2b',
            },
            amber: { // PERINGATAN — kuning emas lembut
                50:'#fdf8e8',100:'#faefc4',200:'#f4e08e',300:'#ebcb52',400:'#dfb52c',
                500:'#c79a14',600:'#a37a0e',700:'#7c5c0f',800:'#654a12',900:'#553f14',
            },
            rose: { // BAHAYA/TOLAK — merah koral tegas (beda dari ruby)
                50:'#fef3f1',100:'#fde4e0',200:'#fbcbc4',300:'#f6a499',400:'#ee7366',
                500:'#e0493c',600:'#c93528',700:'#a82a21',800:'#8a261f',900:'#72231e',
            },
            emerald: { // SUKSES — hijau sage yang tenang (untuk lencana status)
                50:'#f0f9f4',100:'#d9f0e3',200:'#b5e1c9',300:'#85cca7',400:'#52b384',
                500:'#2f9a6c',600:'#237d57',700:'#1f6448',800:'#1c503b',900:'#184132',
            },
            slate: { // NETRAL hangat — menggantikan abu-abu dingin bawaan, supaya semua garis tepi & teks senada
                50:'#fdf8f5',100:'#f8eee8',200:'#f0e0d6',300:'#e2cbbd',400:'#b89d90',
                500:'#8c746a',600:'#6b564d',700:'#4f3d37',800:'#382a26',900:'#241a17',
            },
            ink: { 900:'#2b1520', 800:'#3d1d2d', 700:'#58293f' }, // wine-black untuk teks judul & latar gelap
            gold: { 400:'#d3b05a', 500:'#c19a3f', 600:'#a37e2f' },
=======
            teal: { // UTAMA — iris/lilac pastel
                50:'#f6f4fe',100:'#ece8fb',200:'#dcd5f7',300:'#c3b8ef',400:'#a798e4',
                500:'#8c7bd6',600:'#7767c4',700:'#5f51a6',800:'#4a3f85',900:'#382f66',
            },
            brand: { // AKSEN — peach hangat
                50:'#fff7f0',100:'#ffeadb',200:'#fed5b8',300:'#fbbd92',400:'#f6a470',
                500:'#ec8a52',600:'#d1703a',700:'#a95a30',800:'#85482a',900:'#6a3a25',
            },
            indigo: { // SEKUNDER / info — sakura pink
                50:'#fff4f8',100:'#ffe6ef',200:'#fdccdd',300:'#f8a9c5',400:'#ef82aa',
                500:'#de6393',600:'#c44a7c',700:'#a03a64',800:'#7f3250',900:'#652b42',
            },
            amber: { // PERINGATAN — butter
                50:'#fffbe9',100:'#fff3c4',200:'#ffe590',300:'#fbd161',400:'#f0bb3a',
                500:'#cf9422',600:'#b17c18',700:'#8a6014',800:'#6e4d14',900:'#5a4015',
            },
            rose: { // BAHAYA/TOLAK — koral lembut
                50:'#fff4f2',100:'#ffe5e1',200:'#ffcdc6',300:'#fba89d',400:'#f28275',
                500:'#e57f73',600:'#d2665b',700:'#ae4e45',800:'#8a3a34',900:'#70332e',
            },
            emerald: { // SUKSES — mint
                50:'#f0faf5',100:'#daf3e6',200:'#b8e6cf',300:'#8dd3b1',400:'#62bc93',
                500:'#43a67d',600:'#34896a',700:'#2c6e56',800:'#265945',900:'#214939',
            },
            slate: { // NETRAL bernada lavender-abu, supaya semua garis tepi & teks senada
                50:'#faf9fd',100:'#f3f1f9',200:'#e8e4f1',300:'#d6d0e4',400:'#a49cb8',
                500:'#7a7291',600:'#5f5877',700:'#473f5c',800:'#342e46',900:'#241f33',
            },
            ink: { 900:'#2a2540', 800:'#3a3457', 700:'#4d4570' }, // ungu tua untuk teks judul
            gold: { 400:'#f6a470', 500:'#ec8a52', 600:'#d1703a' },
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        },
    } } }
</script>
