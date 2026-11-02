{{-- ============================================================================
     TEMA TUNGGAL EduVision — dipakai SEMUA halaman (app, login, homepage, beranda)
     supaya warna & font konsisten dan cukup diubah di SATU tempat.

     Arah desain: PASTEL RINGAN bernuansa sekolah — lembut di mata, tetap rapi & berkarakter.
       - Biru seragam  = warna utama. Kunci Tailwind lama "teal" sengaja DIPETAKAN ke sini,
                          jadi semua class bg-teal-600, text-teal-700, dst otomatis ikut berubah.
       - Peach          = aksen hangat ("brand"), Sakura = sekunder ("indigo"),
         Butter = peringatan ("amber"), Koral lembut = bahaya ("rose"), Mint = sukses ("emerald").
       - Netral ("slate") bernada lavender-abu, teks judul ("ink") ungu tua kebiruan agar tetap terbaca.
       Sidebar = biru muda; navbar = putih; footer = biru sangat muda → tiga panel terpisah jelas.
     Font: DM Sans (judul & isi) — konsisten di seluruh halaman.
     ============================================================================ --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('img/logo-eduvision.svg') }}">
@verbatim
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', system-ui, sans-serif; color: #2c3447; }
    body { background-color: #fffdf8; overflow-x: hidden;
        background-image: radial-gradient(900px 520px at 100% -8%, rgba(130,164,214,.16), transparent 60%),
                          radial-gradient(760px 460px at 0% 108%, rgba(249,217,107,.16), transparent 60%);
        background-attachment: fixed; }
    /* Sidebar: biru muda, teks gelap — terpisah dari navbar (putih) dan footer */
    .sidebar-glow { background-image: radial-gradient(360px 280px at 0% 0%, rgba(255,255,255,.75), transparent 62%),
                                      radial-gradient(320px 260px at 100% 100%, rgba(249,217,107,.30), transparent 62%),
                                      linear-gradient(170deg, #eaf0f9 0%, #f1f5fb 55%, #fbece4 100%) !important;
                    border: 1.5px solid #cbd8ea; }
    /* Navbar: lengkung sempurna — garis peach di atas dibuat dengan inset-shadow sehingga
       MENGIKUTI lengkung sudut kartu persis (tidak terpotong/bergerigi) */
    .navbar-card { border: 1.5px solid #cbd8ea; box-shadow: inset 0 4px 0 0 #aac3e6, 0 10px 30px -16px rgba(74,111,170,.22); }
    .menu-ikon { width: 2rem; height: 2rem; border-radius: .6rem; display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.65); color: #5f86c4; flex-shrink: 0; transition: background .2s ease; }
    a:hover > .menu-ikon { background: #fff; }
    .menu-aktif .menu-ikon { background: #e4ecf8; color: #3b588a; }
    /* Kartu berwarna: gradien PASTEL dengan teks gelap (bukan putih), tanpa garis tepi */
    .kartu[class*="bg-gradient"] { border: 0 !important; color: #33415a !important; box-shadow: 0 10px 26px -16px rgba(74,111,170,.30) !important; }
    .kartu[class*="bg-gradient"] * { color: inherit !important; }
    .kartu[class*="bg-gradient"] [class*="bg-white/"] { background: rgba(255,255,255,.62) !important; }
    .kartu[class*="bg-gradient"][class*="from-teal"]    { background-image: linear-gradient(135deg,#e0e9f6,#c3d5ee) !important; }
    .kartu[class*="bg-gradient"][class*="from-indigo"]  { background-image: linear-gradient(135deg,#fde9de,#f9d2bf) !important; color:#643725 !important; }
    .kartu[class*="bg-gradient"][class*="from-brand"]   { background-image: linear-gradient(135deg,#fff4c9,#fde9a0) !important; color:#574213 !important; }
    .kartu[class*="bg-gradient"][class*="from-rose"]    { background-image: linear-gradient(135deg,#ffe1dc,#fbc8bf) !important; color:#70332e !important; }
    .kartu[class*="bg-gradient"][class*="from-emerald"] { background-image: linear-gradient(135deg,#e4efdf,#cde2c5) !important; color:#324632 !important; }
    .kartu[class*="bg-gradient"][class*="from-amber"]   { background-image: linear-gradient(135deg,#fff3c9,#fde79b) !important; color:#5a4015 !important; }
    /* Hero / panel besar bernuansa pastel (dipakai di beranda, homepage, login) */
    .hero-pastel { background-image: linear-gradient(135deg,#eaf0f9 0%,#fbeee6 55%,#fff4c9 100%); color: #33415a; }
    .hero-pastel :is(.text-white,.text-slate-300,.text-slate-400,.text-teal-100,.text-teal-200):not([class*="bg-gradient"]) { color: #414c61 !important; }
    .hero-pastel [class*="bg-white/10"] { background: rgba(255,255,255,.6) !important; }
    .hero-pastel [class*="border-white/"] { border-color: rgba(74,111,170,.25) !important; }
    /* Semua kotak putih di halaman app memakai warna garis tepi YANG SAMA */
    main .bg-white[class*="rounded"][class*="border"] { border-color: #cbd8ea !important; border-width: 1.5px !important; }
    tbody tr:hover { background-color: #f6f8fc !important; }
    img, video, canvas, svg { max-width: 100%; }
    /* Kartu putih: garis tipis + bayangan lembut berlapis */
    .kartu.bg-white { border-width: 1.5px !important; border-color: #cbd8ea !important; border-radius: 1.25rem !important;
        box-shadow: 0 1px 2px rgba(40,55,90,.06), 0 10px 24px -16px rgba(40,55,90,.20); }
    input:not([type=checkbox]):not([type=radio]):not([type=file]), select, textarea { border-color: #b8c8df !important; background-color: #fff; color: #2c3447; }
    ::placeholder { color: #8f99ad !important; opacity: 1; }
    input:focus, select:focus, textarea:focus { border-color: #6f93cb !important; box-shadow: 0 0 0 3px rgba(111,147,203,.20) !important; }
    .border-2 { border-width: 1.5px; }
    thead tr { background: #e4ecf8; border-bottom: 1.5px solid #cbd8ea; }
    thead th { padding-left: .75rem; color: #384157 !important; font-weight: 700; }
    main .border-slate-50, main .border-slate-100 { border-color: #e8eef7 !important; }
    main .text-slate-300 { color: #8f99ad; }

    /* ===== Tombol & lencana berwarna SOLID -> pastel lembut + teks gelap + garis tepi jelas =====
       (supaya tulisan selalu terbaca; tidak ada lagi teks putih di atas warna pastel) */
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-teal"].text-white:not(.kartu) { background-image: none !important; background-color: #cddcf1 !important; color: #2a3b57 !important; border: 1.5px solid #93b1dd !important; box-shadow: none !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu),
    [class*="bg-gradient"][class*="from-indigo"].text-white:not(.kartu) { background-image: none !important; background-color: #fbdcc9 !important; color: #6b3a22 !important; border: 1.5px solid #eeaf8c !important; box-shadow: none !important; }
    :is(.bg-amber-400,.bg-amber-500,.bg-amber-600).text-white:not(.kartu) { background-color: #fde9a8 !important; color: #5a4015 !important; border: 1.5px solid #e9c457 !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu) { background-color: #fcd0c9 !important; color: #6a2b26 !important; border: 1.5px solid #f0a499 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu) { background-color: #d9e9d2 !important; color: #2f4a2d !important; border: 1.5px solid #a8c99c !important; }
    :is(.bg-slate-700,.bg-slate-800,.bg-slate-900).text-white:not(.kartu) { background-color: #e6ebf3 !important; color: #2c3447 !important; border: 1.5px solid #c8d1df !important; }
    :is(.bg-brand-400,.bg-brand-500,.bg-brand-600),[class*="bg-gradient"][class*="from-brand-5"] { background-image: none !important; background-color: #fde9a0 !important; color: #574213 !important; border: 1.5px solid #efc23f !important; box-shadow: none !important; }
    :is(.bg-teal-500,.bg-teal-600,.bg-teal-700).text-white:not(.kartu):hover { background-color: #b6cbe8 !important; }
    :is(.bg-indigo-500,.bg-indigo-600,.bg-indigo-700).text-white:not(.kartu):hover { background-color: #f8cdb4 !important; }
    :is(.bg-rose-500,.bg-rose-600,.bg-rose-700).text-white:not(.kartu):hover { background-color: #f9bfb6 !important; }
    :is(.bg-emerald-500,.bg-emerald-600,.bg-emerald-700).text-white:not(.kartu):hover { background-color: #c9dfc0 !important; }
        /* Tombol aksi ukuran besar untuk halaman detail: rata, sama lebar, rapi */
    .aksi-besar { display:flex; width:100%; max-width:26rem; gap:.75rem; margin-left:auto; }
    .aksi-besar > a, .aksi-besar > .aksi-form { flex:1 1 0; display:flex; }
    .aksi-besar .btn-aksi { flex:1; justify-content:center; padding:.65rem 1rem; font-size:.9rem; min-height:2.75rem; }
    /* Tombol aksi baku Detail / Edit / Hapus (komponen <x-aksi>) */
    .btn-aksi { display: inline-flex; align-items: center; gap: .35rem; padding: .38rem .75rem; border-radius: .6rem; font-size: .75rem; font-weight: 700; border: 1.5px solid; line-height: 1; white-space: nowrap; transition: background-color .15s, transform .15s; }
    .btn-aksi:hover { transform: translateY(-1px); }
    .btn-detail { background: #e4ecf8; border-color: #93b1dd; color: #34507c; }  .btn-detail:hover { background: #c3d5ee; }
    .btn-edit   { background: #fdf0c2; border-color: #e5c260; color: #5a4015; }  .btn-edit:hover   { background: #fae59a; }
    .btn-hapus  { background: #fde1dc; border-color: #eea99f; color: #7a2f28; }  .btn-hapus:hover  { background: #fbcdc5; }
    /* Responsif: pada layar sempit, kolom form bertumpuk, form pencarian boleh turun baris,
       dan input berlebar tetap tidak melebihi layar. */
    .w-64 { max-width: 100%; }
    main form.flex { flex-wrap: wrap; }
    @media (max-width: 420px) { main form .grid.grid-cols-2 { grid-template-columns: 1fr; } }
    h1, h2, h3, h4, .font-display { font-family: 'DM Sans', system-ui, sans-serif; letter-spacing: -0.01em; font-weight: 700; }
    /* ===== Tidak ada teks putih: semua teks 'putih' menjadi gelap hangat (kecuali tombol tutup di atas latar gelap foto) ===== */
    .text-white, [class*="hover:text-white"]:hover { color: #2c3447 !important; }
    .text-white svg, .text-white * { color: inherit; }
    body { background-image: radial-gradient(900px 520px at 100% -8%, rgba(130,164,214,.16), transparent 60%), radial-gradient(760px 460px at 0% 108%, rgba(249,217,107,.20), transparent 60%); }
    /* Nuansa buku tulis: kertas krem berpola titik halus */
    body { background-color: #fffdf8;
        background-image: radial-gradient(#dde5f1 1px, transparent 1.2px), radial-gradient(900px 520px at 100% -8%, rgba(130,164,214,.14), transparent 60%), radial-gradient(760px 460px at 0% 108%, rgba(249,217,107,.16), transparent 60%);
        background-size: 22px 22px, auto, auto; background-attachment: fixed; }
    /* ===== Tombol aksi: selalu 3 sejajar satu baris (desktop maupun HP) ===== */
    .aksi-grup { display: inline-flex; flex-wrap: nowrap; align-items: center; justify-content: flex-end; gap: .375rem; white-space: nowrap; }
    .aksi-grup > .aksi-form { display: inline-flex; margin: 0; }
    .aksi-grup .btn-aksi { justify-content: center; }
    td:has(> .aksi-grup), td:has(> div > .aksi-grup) { white-space: nowrap; }
    @media (max-width: 480px) { .aksi-grup { gap: .25rem; } .aksi-grup .btn-aksi { padding: .34rem .5rem; font-size: .68rem; gap: .2rem; } .aksi-grup .btn-aksi svg { width: .8rem; height: .8rem; } }
    /* ===== Dialog konfirmasi bergaya tema ===== */
    .edu-dialog { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(41,59,92,.28); backdrop-filter: blur(3px); }
    .edu-dialog[hidden] { display: none; }
    .edu-dialog-kotak { width: 100%; max-width: 24rem; background: #fffdf8; border: 1.5px solid #cbd8ea; border-radius: 1.25rem; padding: 1.5rem; text-align: center; box-shadow: 0 24px 50px -20px rgba(41,59,92,.45); }
    .edu-dialog-ikon { width: 3.25rem; height: 3.25rem; margin: 0 auto .75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: #e4ecf8; color: #3b588a; border: 1.5px solid #aac3e6; }
    .edu-bahaya .edu-dialog-ikon { background: #fde1dc; color: #7a2f28; border-color: #eea99f; }
    .edu-dialog-judul { font-size: 1.05rem; color: #232c3d; margin: 0; }
    .edu-dialog-isi { font-size: .85rem; color: #465065; margin: .5rem 0 1.1rem; line-height: 1.5; }
    .edu-dialog-aksi { display: flex; gap: .5rem; justify-content: center; }
    .edu-dialog-aksi .btn-aksi { padding: .55rem 1.1rem; font-size: .8rem; }
    body.edu-dialog-buka { overflow: hidden; }
    /* ===== Tombol seragam: biru = utama (masuk/buka/simpan/tambah), netral = kembali/batal; selalu ikon + tulisan ===== */
    .tombol-aktif, .btn-utama, .btn-sekunder { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; white-space: nowrap; line-height: 1.2; }
    .btn-utama, .btn-sekunder { padding: .6rem 1.15rem; border-radius: .75rem; font-size: .875rem; font-weight: 700; border: 1.5px solid; transition: background-color .15s, transform .1s; }
    .btn-utama { background: #cddcf1; border-color: #93b1dd; color: #1f3a5f; }
    .btn-utama:hover { background: #b6cbe8; transform: translateY(-1px); }
    .btn-sekunder, .bg-slate-100.btn-sekunder { background: #fffdf8 !important; border: 1.5px solid #b8c8df !important; color: #34507c !important; padding: .6rem 1.15rem; border-radius: .75rem !important; font-size: .875rem; font-weight: 700; }
    .btn-sekunder:hover, .bg-slate-100.btn-sekunder:hover { background: #e4ecf8 !important; }
    .btn-besar { padding: .85rem 1.7rem; font-size: .95rem; }
    .btn-utama svg, .btn-sekunder svg, .tombol-aktif svg { flex-shrink: 0; }
</style>
<script>
    tailwind.config = { theme: { extend: {
        fontFamily: {
            sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
            display: ['"DM Sans"', 'system-ui', 'sans-serif'],
        },
        colors: {
            teal: { // UTAMA — biru seragam pastel (denim)
                50:'#f6f8fc',100:'#e4ecf8',200:'#cddcf1',300:'#aac3e6',400:'#82a4d6',
                500:'#6f93cb',600:'#4a6faa',700:'#3b588a',800:'#314870',900:'#293b5c',
            },
            brand: { // AKSEN — kuning pensil
                50:'#fffbeb',100:'#fff4c9',200:'#fde9a0',300:'#f9d96b',400:'#efc23f',
                500:'#d9a521',600:'#b3861a',700:'#8a6716',800:'#6c5214',900:'#574213',
            },
            indigo: { // SEKUNDER — bata (gedung sekolah)
                50:'#fff6f1',100:'#fde9de',200:'#f9d2bf',300:'#f3b698',400:'#e89572',
                500:'#d97a52',600:'#bf6339',700:'#9a4f2e',800:'#7b4129',900:'#643725',
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
                50:'#f9fafc',100:'#f0f3f8',200:'#dfe5ee',300:'#c8d1df',400:'#737e96',
                500:'#59647a',600:'#465065',700:'#384157',800:'#2c3447',900:'#1f2635',
            },
            ink: { 900:'#232c3d', 800:'#2f394c', 700:'#414c61' }, // ungu tua untuk teks judul
            gold: { 400:'#efc23f', 500:'#d9a521', 600:'#b3861a' },
        },
    } } }
</script>
@endverbatim
