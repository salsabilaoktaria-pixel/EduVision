{{-- ============================================================================
     TEMA TUNGGAL EduVision — dipakai SEMUA halaman (app, login, homepage, beranda)
     supaya warna & font konsisten dan cukup diubah di SATU tempat.

     Arah desain: PASTEL RINGAN bernuansa sekolah — lembut di mata, tetap rapi & berkarakter.
       - Powder blue   = warna utama. Kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                          jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah.
       - Peach          = aksen hangat ("brand"), Sakura = sekunder ("indigo"),
         Butter = peringatan ("amber"), Koral lembut = bahaya ("rose"), Mint = sukses ("emerald").
       - Netral ("slate") bernada lavender-abu, teks judul ("ink") ungu tua kebiruan agar tetap terbaca.
       Sidebar = biru muda; navbar = putih; footer = biru sangat muda → tiga panel terpisah jelas.
     Font: DM Sans (bersih, modern, mudah dibaca).
     ============================================================================ --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('img/logo-eduvision.svg') }}">
@verbatim
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', system-ui, sans-serif; color: #262c36; }
    body { background-color: #fafbfd; overflow-x: hidden;
        background-image: radial-gradient(900px 520px at 100% -8%, rgba(106,159,208,.16), transparent 60%),
                          radial-gradient(760px 460px at 0% 108%, rgba(251,189,146,.16), transparent 60%);
        background-attachment: fixed; }
    /* Sidebar: biru muda, teks gelap — terpisah dari navbar (putih) dan footer */
    .sidebar-glow { background-image: radial-gradient(360px 280px at 0% 0%, rgba(255,255,255,.75), transparent 62%),
                                      radial-gradient(320px 260px at 100% 100%, rgba(251,189,146,.30), transparent 62%),
                                      linear-gradient(170deg, #e9f2fa 0%, #f1f5f9 55%, #fbece4 100%) !important;
                    border: 1.5px solid #c6d6e6; }
    /* Navbar: lengkung sempurna — garis peach di atas dibuat dengan inset-shadow sehingga
       MENGIKUTI lengkung sudut kartu persis (tidak terpotong/bergerigi) */
    .navbar-card { border: 1.5px solid #c6d6e6; box-shadow: inset 0 4px 0 0 #f7c9a8, 0 10px 30px -16px rgba(79,127,176,.22); }
    .menu-ikon { width: 2rem; height: 2rem; border-radius: .6rem; display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.65); color: #5f93c6; flex-shrink: 0; transition: background .2s ease; }
    a:hover > .menu-ikon { background: #fff; }
    .menu-aktif .menu-ikon { background: #e6f0f9; color: #5f51a6; }
    /* Kartu berwarna: gradien PASTEL dengan teks gelap (bukan putih), tanpa garis tepi */
    .kartu[class*="bg-gradient"] { border: 0 !important; color: #2f3d4d !important; box-shadow: 0 10px 26px -16px rgba(79,127,176,.30) !important; }
    .kartu[class*="bg-gradient"] * { color: inherit !important; }
    .kartu[class*="bg-gradient"] [class*="bg-white/"] { background: rgba(255,255,255,.62) !important; }
    .kartu[class*="bg-gradient"][class*="from-teal"]    { background-image: linear-gradient(135deg,#e2eef8,#d2c8f5) !important; }
    .kartu[class*="bg-gradient"][class*="from-indigo"]  { background-image: linear-gradient(135deg,#fde4ee,#f9ccdf) !important; color:#5f2a47 !important; }
    .kartu[class*="bg-gradient"][class*="from-brand"]   { background-image: linear-gradient(135deg,#ffe9d8,#fdd2b4) !important; color:#6a3a25 !important; }
    .kartu[class*="bg-gradient"][class*="from-rose"]    { background-image: linear-gradient(135deg,#ffe1dc,#fbc8bf) !important; color:#70332e !important; }
    .kartu[class*="bg-gradient"][class*="from-emerald"] { background-image: linear-gradient(135deg,#daf3e6,#bfe6d3) !important; color:#214939 !important; }
    .kartu[class*="bg-gradient"][class*="from-amber"]   { background-image: linear-gradient(135deg,#fff3c9,#fde79b) !important; color:#5a4015 !important; }
    /* Hero / panel besar bernuansa pastel (dipakai di beranda, homepage, login) */
    .hero-pastel { background-image: linear-gradient(135deg,#e9f2fa 0%,#fbeee6 55%,#fdebdd 100%); color: #2f3d4d; }
    .hero-pastel :is(.text-white,.text-slate-300,.text-slate-400,.text-teal-100,.text-teal-200):not([class*="bg-gradient"]) { color: #4d4570 !important; }
    .hero-pastel [class*="bg-white/10"] { background: rgba(255,255,255,.6) !important; }
    .hero-pastel [class*="border-white/"] { border-color: rgba(79,127,176,.25) !important; }
    /* Semua kotak putih di halaman app memakai warna garis tepi YANG SAMA */
    main .bg-white[class*="rounded"][class*="border"] { border-color: #c6d6e6 !important; border-width: 1.5px !important; }
    tbody tr:hover { background-color: #f4f8fc !important; }
    img, video, canvas, svg { max-width: 100%; }
    /* Kartu putih: garis tipis + bayangan lembut berlapis */
    .kartu.bg-white { border-width: 1.5px !important; border-color: #c6d6e6 !important; border-radius: 1.25rem !important;
        box-shadow: 0 1px 2px rgba(50,70,100,.06), 0 10px 24px -16px rgba(50,70,100,.20); }
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { border-color: #b3c7dc !important; background-color: #fff; color: #262c36; }
    ::placeholder { color: #8a93a1 !important; opacity: 1; }
    input:focus, select:focus, textarea:focus { border-color: #6a9fd0 !important; box-shadow: 0 0 0 3px rgba(95,147,198,.20) !important; }
    .border-2 { border-width: 1.5px; }
    thead tr { background: #e6f0f9; border-bottom: 1.5px solid #c6d6e6; }
    thead th { padding-left: .75rem; color: #343c49 !important; font-weight: 700; }
    main .border-slate-50, main .border-slate-100 { border-color: #e1ebf4 !important; }
    main .text-slate-300 { color: #8a93a1; }

    /* ===== Tombol & lencana berwarna SOLID -> pastel lembut + teks gelap + garis tepi jelas =====
       (supaya tulisan selalu terbaca; tidak ada lagi teks putih di atas warna pastel) */
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-teal"].text-white:not(.kartu) { background-image: none !important; background-color: #d3e6f5 !important; color: #24384f !important; border: 1.5px solid #9cc3e3 !important; box-shadow: none !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-indigo"].text-white:not(.kartu) { background-image: none !important; background-color: #fbd3e2 !important; color: #5a2443 !important; border: 1.5px solid #efa6c3 !important; box-shadow: none !important; }
    :is(.bg-amber-400,.bg-amber-500,.bg-amber-600).text-white:not(.kartu) { background-color: #fde9a8 !important; color: #5a4015 !important; border: 1.5px solid #e9c457 !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu) { background-color: #fcd0c9 !important; color: #6a2b26 !important; border: 1.5px solid #f0a499 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu) { background-color: #c8ecd9 !important; color: #1d4636 !important; border: 1.5px solid #8fcdb0 !important; }
    :is(.bg-slate-700,.bg-slate-800,.bg-slate-900).text-white:not(.kartu) { background-color: #e6ebf2 !important; color: #262c36 !important; border: 1.5px solid #c3cdda !important; }
    :is(.bg-brand-400,.bg-brand-500,.bg-brand-600),[class*="bg-gradient"][class*="from-brand-5"] { background-image: none !important; background-color: #fdd2b4 !important; color: #5a2f1c !important; border: 1.5px solid #f2a97c !important; box-shadow: none !important; }
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu):hover { background-color: #bcd6ee !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu):hover { background-color: #f8c1d6 !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu):hover { background-color: #f9bfb6 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu):hover { background-color: #b6e3cb !important; }
    /* Tombol aksi baku Detail / Edit / Hapus (komponen <x-aksi>) */
    .btn-aksi { display: inline-flex; align-items: center; gap: .35rem; padding: .38rem .75rem; border-radius: .6rem; font-size: .75rem; font-weight: 700; border: 1.5px solid; line-height: 1; white-space: nowrap; transition: background-color .15s, transform .15s; }
    .btn-aksi:hover { transform: translateY(-1px); }
    .btn-detail { background: #e6f0f9; border-color: #9cc3e3; color: #2f4f70; }  .btn-detail:hover { background: #c4dbf0; }
    .btn-edit   { background: #fdf0c2; border-color: #e5c260; color: #5a4015; }  .btn-edit:hover   { background: #fae59a; }
    .btn-hapus  { background: #fde1dc; border-color: #eea99f; color: #7a2f28; }  .btn-hapus:hover  { background: #fbcdc5; }
    /* Responsif: pada layar sempit, kolom form bertumpuk, form pencarian boleh turun baris,
       dan input berlebar tetap tidak melebihi layar. */
    .w-64 { max-width: 100%; }
    main form.flex { flex-wrap: wrap; }
    @media (max-width: 420px) { main form .grid.grid-cols-2 { grid-template-columns: 1fr; } }
    h1, h2, h3, .font-display { font-family: 'DM Sans', system-ui, sans-serif; letter-spacing: -0.01em; font-weight: 700; }
    /* ===== Tidak ada teks putih: semua teks 'putih' menjadi gelap hangat (kecuali tombol tutup di atas latar gelap foto) ===== */
    .text-white, [class*="hover:text-white"]:hover { color: #262c36 !important; }
    .text-white svg, .text-white * { color: inherit; }
    body { background-image: radial-gradient(900px 520px at 100% -8%, rgba(106,159,208,.16), transparent 60%), radial-gradient(760px 460px at 0% 108%, rgba(251,189,146,.20), transparent 60%); }
</style>
<script>
    tailwind.config = { theme: { extend: {
        fontFamily: {
            sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
            display: ['"DM Sans"', 'system-ui', 'sans-serif'],
        },
        colors: {
            teal: { // UTAMA — powder blue pastel
                50:'#f4f8fc',100:'#e6f0f9',200:'#d3e6f5',300:'#b4d2ea',400:'#86b3da',
                500:'#6a9fd0',600:'#4f7fb0',700:'#3d6590',800:'#34526f',900:'#2b4660',
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
            slate: { // NETRAL lavender-abu: 50-200 untuk latar & garis tepi (cukup tegas), 400+ untuk teks (kontras terbaca)
                50:'#f8fafc',100:'#f0f3f7',200:'#dbe1ea',300:'#c3cdda',400:'#76808f',
                500:'#586271',600:'#434c5a',700:'#343c49',800:'#262c36',900:'#1b2029',
            },
            ink: { 900:'#27313d', 800:'#323d4b', 700:'#43505f' }, // ungu tua untuk teks judul
            gold: { 400:'#f6a470', 500:'#ec8a52', 600:'#d1703a' },
        },
    } } }
</script>
@endverbatim
