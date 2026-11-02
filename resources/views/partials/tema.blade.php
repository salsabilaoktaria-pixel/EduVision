{{-- ============================================================================
     TEMA TUNGGAL EduVision — dipakai SEMUA halaman (app, login, homepage, beranda)
     supaya warna & font konsisten dan cukup diubah di SATU tempat.

     Arah desain: PASTEL RINGAN bernuansa sekolah — lembut di mata, tetap rapi & berkarakter.
       - Iris (lilac)   = warna utama. Kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                          jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah.
       - Peach          = aksen hangat ("brand"), Sakura = sekunder ("indigo"),
         Butter = peringatan ("amber"), Koral lembut = bahaya ("rose"), Mint = sukses ("emerald").
       - Netral ("slate") bernada lavender-abu, teks judul ("ink") ungu tua kebiruan agar tetap terbaca.
       Sidebar = lilac muda; navbar = putih; footer = lilac sangat muda → tiga panel terpisah jelas.
     Font: Bricolage Grotesque (judul, berkarakter) + DM Sans (isi, mudah dibaca).
     ============================================================================ --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', system-ui, sans-serif; }
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
        },
    } } }
</script>
