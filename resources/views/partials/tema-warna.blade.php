{{-- ============================================================================
     TEMA TUNGGAL EduVision — dipakai SEMUA halaman (app, login, homepage, beranda)
     supaya warna & font konsisten dan cukup diubah di SATU tempat.

     Arah desain: PASTEL RINGAN bernuansa sekolah — lembut di mata, tetap rapi & berkarakter.
       - Rose quartz   = warna utama. Kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                          jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah.
       - Peach          = aksen hangat ("brand"), Sakura = sekunder ("indigo"),
         Butter = peringatan ("amber"), Koral lembut = bahaya ("rose"), Mint = sukses ("emerald").
       - Netral ("slate") bernada lavender-abu, teks judul ("ink") ungu tua kebiruan agar tetap terbaca.
       Sidebar = rose muda; navbar = putih; footer = rose sangat muda → tiga panel terpisah jelas.
     Font: DM Sans (bersih, modern, mudah dibaca).
     ============================================================================ --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('img/logo-eduvision.svg') }}">
@verbatim
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', system-ui, sans-serif; color: #342d29; }
    body { background-color: #fffaf7; overflow-x: hidden;
        background-image: radial-gradient(900px 520px at 100% -8%, rgba(234,149,164,.16), transparent 60%),
                          radial-gradient(760px 460px at 0% 108%, rgba(242,203,138,.16), transparent 60%);
        background-attachment: fixed; }
    /* Sidebar: rose muda, teks gelap — terpisah dari navbar (putih) dan footer */
    .sidebar-glow { background-image: radial-gradient(360px 280px at 0% 0%, rgba(255,255,255,.75), transparent 62%),
                                      radial-gradient(320px 260px at 100% 100%, rgba(242,203,138,.30), transparent 62%),
                                      linear-gradient(170deg, #fdeeef 0%, #fbefeb 55%, #fbece4 100%) !important;
                    border: 1.5px solid #e6cfca; }
    /* Navbar: lengkung sempurna — garis peach di atas dibuat dengan inset-shadow sehingga
       MENGIKUTI lengkung sudut kartu persis (tidak terpotong/bergerigi) */
    .navbar-card { border: 1.5px solid #e6cfca; box-shadow: inset 0 4px 0 0 #f4b8c1, 0 10px 30px -16px rgba(194,92,114,.22); }
    .menu-ikon { width: 2rem; height: 2rem; border-radius: .6rem; display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.65); color: #cf6a80; flex-shrink: 0; transition: background .2s ease; }
    a:hover > .menu-ikon { background: #fff; }
    .menu-aktif .menu-ikon { background: #fde9ec; color: #a04960; }
    /* Kartu berwarna: gradien PASTEL dengan teks gelap (bukan putih), tanpa garis tepi */
    .kartu[class*="bg-gradient"] { border: 0 !important; color: #4a3a38 !important; box-shadow: 0 10px 26px -16px rgba(194,92,114,.30) !important; }
    .kartu[class*="bg-gradient"] * { color: inherit !important; }
    .kartu[class*="bg-gradient"] [class*="bg-white/"] { background: rgba(255,255,255,.62) !important; }
    .kartu[class*="bg-gradient"][class*="from-teal"]    { background-image: linear-gradient(135deg,#fce4e8,#f8c9d1) !important; }
    .kartu[class*="bg-gradient"][class*="from-indigo"]  { background-image: linear-gradient(135deg,#e7eef3,#d2dfe8) !important; color:#324453 !important; }
    .kartu[class*="bg-gradient"][class*="from-brand"]   { background-image: linear-gradient(135deg,#fdf0d8,#f9e0b5) !important; color:#5e411f !important; }
    .kartu[class*="bg-gradient"][class*="from-rose"]    { background-image: linear-gradient(135deg,#ffe1dc,#fbc8bf) !important; color:#70332e !important; }
    .kartu[class*="bg-gradient"][class*="from-emerald"] { background-image: linear-gradient(135deg,#e4efdf,#cde2c5) !important; color:#324632 !important; }
    .kartu[class*="bg-gradient"][class*="from-amber"]   { background-image: linear-gradient(135deg,#fff3c9,#fde79b) !important; color:#5a4015 !important; }
    /* Hero / panel besar bernuansa pastel (dipakai di beranda, homepage, login) */
    .hero-pastel { background-image: linear-gradient(135deg,#fdeeef 0%,#fbeee6 55%,#fdf0d8 100%); color: #4a3a38; }
    .hero-pastel :is(.text-white,.text-slate-300,.text-slate-400,.text-teal-100,.text-teal-200):not([class*="bg-gradient"]) { color: #4f4340 !important; }
    .hero-pastel [class*="bg-white/10"] { background: rgba(255,255,255,.6) !important; }
    .hero-pastel [class*="border-white/"] { border-color: rgba(194,92,114,.25) !important; }
    /* Semua kotak putih di halaman app memakai warna garis tepi YANG SAMA */
    main .bg-white[class*="rounded"][class*="border"] { border-color: #e6cfca !important; border-width: 1.5px !important; }
    tbody tr:hover { background-color: #fdf4f2 !important; }
    img, video, canvas, svg { max-width: 100%; }
    /* Kartu putih: garis tipis + bayangan lembut berlapis */
    .kartu.bg-white { border-width: 1.5px !important; border-color: #e6cfca !important; border-radius: 1.25rem !important;
        box-shadow: 0 1px 2px rgba(100,60,60,.06), 0 10px 24px -16px rgba(100,60,60,.20); }
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { border-color: #dcc2bb !important; background-color: #fff; color: #342d29; }
    ::placeholder { color: #9a8e86 !important; opacity: 1; }
    input:focus, select:focus, textarea:focus { border-color: #dc7589 !important; box-shadow: 0 0 0 3px rgba(220,117,137,.20) !important; }
    .border-2 { border-width: 1.5px; }
    thead tr { background: #fde9ec; border-bottom: 1.5px solid #e6cfca; }
    thead th { padding-left: .75rem; color: #433b36 !important; font-weight: 700; }
    main .border-slate-50, main .border-slate-100 { border-color: #f1e5e0 !important; }
    main .text-slate-300 { color: #9a8e86; }

    /* ===== Tombol & lencana berwarna SOLID -> pastel lembut + teks gelap + garis tepi jelas =====
       (supaya tulisan selalu terbaca; tidak ada lagi teks putih di atas warna pastel) */
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-teal"].text-white:not(.kartu) { background-image: none !important; background-color: #fad5da !important; color: #5a2b38 !important; border: 1.5px solid #ec9fae !important; box-shadow: none !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-indigo"].text-white:not(.kartu) { background-image: none !important; background-color: #dfe9f0 !important; color: #2f4658 !important; border: 1.5px solid #a9c0d1 !important; box-shadow: none !important; }
    :is(.bg-amber-400,.bg-amber-500,.bg-amber-600).text-white:not(.kartu) { background-color: #fde9a8 !important; color: #5a4015 !important; border: 1.5px solid #e9c457 !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu) { background-color: #fcd0c9 !important; color: #6a2b26 !important; border: 1.5px solid #f0a499 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu) { background-color: #d9e9d2 !important; color: #2f4a2d !important; border: 1.5px solid #a8c99c !important; }
    :is(.bg-slate-700,.bg-slate-800,.bg-slate-900).text-white:not(.kartu) { background-color: #efe8e2 !important; color: #342d29 !important; border: 1.5px solid #d3c7be !important; }
    :is(.bg-brand-400,.bg-brand-500,.bg-brand-600),[class*="bg-gradient"][class*="from-brand-5"] { background-image: none !important; background-color: #f9e0b5 !important; color: #5e411f !important; border: 1.5px solid #e8b362 !important; box-shadow: none !important; }
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu):hover { background-color: #f4bcc6 !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu):hover { background-color: #d2e0ea !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu):hover { background-color: #f9bfb6 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu):hover { background-color: #c9dfc0 !important; }
    /* Tombol aksi baku Detail / Edit / Hapus (komponen <x-aksi>) */
    .btn-aksi { display: inline-flex; align-items: center; gap: .35rem; padding: .38rem .75rem; border-radius: .6rem; font-size: .75rem; font-weight: 700; border: 1.5px solid; line-height: 1; white-space: nowrap; transition: background-color .15s, transform .15s; }
    .btn-aksi:hover { transform: translateY(-1px); }
    .btn-detail { background: #fde9ec; border-color: #ec9fae; color: #7a3a4a; }  .btn-detail:hover { background: #f8c9d1; }
    .btn-edit   { background: #fdf0c2; border-color: #e5c260; color: #5a4015; }  .btn-edit:hover   { background: #fae59a; }
    .btn-hapus  { background: #fde1dc; border-color: #eea99f; color: #7a2f28; }  .btn-hapus:hover  { background: #fbcdc5; }
    /* Responsif: pada layar sempit, kolom form bertumpuk, form pencarian boleh turun baris,
       dan input berlebar tetap tidak melebihi layar. */
    .w-64 { max-width: 100%; }
    main form.flex { flex-wrap: wrap; }
    @media (max-width: 420px) { main form .grid.grid-cols-2 { grid-template-columns: 1fr; } }
    h1, h2, h3, .font-display { font-family: 'DM Sans', system-ui, sans-serif; letter-spacing: -0.01em; font-weight: 700; }
    /* ===== Tidak ada teks putih: semua teks 'putih' menjadi gelap hangat (kecuali tombol tutup di atas latar gelap foto) ===== */
    .text-white, [class*="hover:text-white"]:hover { color: #342d29 !important; }
    .text-white svg, .text-white * { color: inherit; }
    body { background-image: radial-gradient(900px 520px at 100% -8%, rgba(234,149,164,.16), transparent 60%), radial-gradient(760px 460px at 0% 108%, rgba(242,203,138,.20), transparent 60%); }
</style>
<script>
    tailwind.config = { theme: { extend: {
        fontFamily: {
            sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
            display: ['"DM Sans"', 'system-ui', 'sans-serif'],
        },
        colors: {
            teal: { // UTAMA — rose quartz pastel
                50:'#fdf4f2',100:'#fde9ec',200:'#fad5da',300:'#f4b8c1',400:'#ea95a4',
                500:'#dc7589',600:'#c25c72',700:'#a04960',800:'#833d50',900:'#6b3442',
            },
            brand: { // AKSEN — peach hangat
                50:'#fffaf0',100:'#fdf0d8',200:'#f9e0b5',300:'#f2cb8a',400:'#e8b362',
                500:'#d99c42',600:'#b9802f',700:'#926526',800:'#744f22',900:'#5e411f',
            },
            indigo: { // SEKUNDER / info — sakura pink
                50:'#f5f8fa',100:'#e7eef3',200:'#d2dfe8',300:'#b3c8d7',400:'#8fadc3',
                500:'#6f92ad',600:'#587a95',700:'#476379',800:'#3b5163',900:'#324453',
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
                50:'#f4f8f2',100:'#e4efdf',200:'#cde2c5',300:'#aecfa3',400:'#8cb97f',
                500:'#6fa162',600:'#588650',700:'#486b42',800:'#3c5539',900:'#324632',
            },
            slate: { // NETRAL lavender-abu: 50-200 untuk latar & garis tepi (cukup tegas), 400+ untuk teks (kontras terbaca)
                50:'#fbf8f6',100:'#f4efeb',200:'#e6ddd6',300:'#d3c7be',400:'#857a72',
                500:'#6a6059',600:'#544b45',700:'#433b36',800:'#342d29',900:'#26211e',
            },
            ink: { 900:'#2e2523', 800:'#3d3330', 700:'#4f4340' }, // ungu tua untuk teks judul
            gold: { 400:'#e8b362', 500:'#d99c42', 600:'#b9802f' },
        },
    } } }
</script>
@endverbatim
