{{-- Pemuat CSS Tailwind. Normalnya memakai file hasil build (public/css/app.css): cepat, tanpa internet.
     Kalau file itu tidak ada, otomatis jatuh ke mode CDN lama (lambat, perlu internet). Taruh di akhir <head>. --}}
@if(file_exists(public_path('css/app.css')))
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
@else
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
@verbatim
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
@endif
