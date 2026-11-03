<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, fotoModalUrl: null }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - EduVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; }
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
            background: conic-gradient(from var(--shimmer-angle, 0deg), transparent 0%, #2dd4bf 8%, #a78bfa 16%, transparent 28%);
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


        /* ===== EDUVISION DARK LUXURY — GLOBAL REDESIGN ===== */
        :root {
            color-scheme: dark;
            --ev-bg: #080A14;
            --ev-panel: #111426;
            --ev-panel-2: #171A30;
            --ev-line: #2D2850;
            --ev-purple: #8B5CF6;
            --ev-purple-bright: #A78BFA;
            --ev-text: #F2EEFF;
            --ev-muted: #A7A3C4;
        }
        html { background: var(--ev-bg); }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: radial-gradient(ellipse at 75% 0%, #1b1240 0%, #0b0d1b 38%, #080a14 78%) !important;
            color: var(--ev-text) !important;
        }
        ::-webkit-scrollbar-thumb { background: #6D45B8 !important; }
        ::-webkit-scrollbar-track { background: #0b0d1b !important; }

        /* Sidebar intentionally distinct: deep violet, fixed visual divider */
        body > div > aside,
        .flex.min-h-screen > aside {
            background: linear-gradient(180deg, #211443 0%, #17102F 48%, #0E1022 100%) !important;
            border-right: 1px solid #6740B8 !important;
            box-shadow: 12px 0 36px rgba(0,0,0,.28), inset -1px 0 0 rgba(167,139,250,.12) !important;
        }
        aside .border-b { border-color: rgba(196,181,253,.16) !important; }
        aside nav a { border-radius: 12px !important; transition: background .2s ease, color .2s ease, transform .2s ease !important; }
        aside nav a:hover { background: rgba(167,139,250,.12) !important; color: #fff !important; transform: translateX(2px); }
        aside nav a.bg-brand-600\/20,
        aside nav a[class*="border-brand-500"] {
            background: linear-gradient(100deg, #7C3AED 0%, #5B21B6 100%) !important;
            color: #fff !important;
            border-color: #C4B5FD !important;
            box-shadow: 0 8px 22px rgba(124,58,237,.25) !important;
        }
        aside p.text-slate-500 { color: #8D82B7 !important; }
        aside .text-slate-300, aside .text-slate-400 { color: #C7C0E5 !important; }

        /* Top bar and main content are navy/charcoal, not the sidebar purple */
        header {
            background: rgba(12,14,29,.96) !important;
            border-bottom: 1px solid #2C2548 !important;
            box-shadow: 0 8px 24px rgba(0,0,0,.18) !important;
            backdrop-filter: blur(16px);
        }
        main {
            background: transparent !important;
            color: var(--ev-text) !important;
        }
        footer {
            background: #0D1020 !important;
            color: #AAA5C7 !important;
            border-top: 1px solid #2D2850 !important;
        }

        /* Every dashboard card and white panel becomes a premium dark surface */
        main .kartu,
        main .bg-white,
        main [class~="bg-slate-50"],
        main [class~="bg-slate-100"],
        main [class~="bg-gray-50"],
        main [class~="bg-gray-100"] {
            background-color: #121528 !important;
            background-image: none !important;
            color: #F2EEFF !important;
            border-color: #30294F !important;
            box-shadow: 0 12px 30px rgba(0,0,0,.20), inset 0 1px 0 rgba(255,255,255,.025) !important;
        }
        main .kartu { border-radius: 18px !important; }
        main .kartu:hover { transform: translateY(-3px); box-shadow: 0 18px 38px rgba(0,0,0,.32), 0 0 0 1px rgba(139,92,246,.18) !important; }
        main .kartu::before { background: conic-gradient(from var(--shimmer-angle, 0deg), transparent 0%, #8B5CF6 10%, #C4B5FD 18%, transparent 30%) !important; }

        /* Dashboard metric cards: cohesive charcoal cards with violet accent */
        main .grid > .kartu[class*="bg-gradient-to-br"] {
            background: linear-gradient(145deg, #19172F 0%, #111426 72%) !important;
            border: 1px solid #39305D !important;
            border-left: 3px solid #8B5CF6 !important;
        }
        main .grid > .kartu[class*="bg-gradient-to-br"]:nth-child(2) { border-left-color: #60A5FA !important; }
        main .grid > .kartu[class*="bg-gradient-to-br"]:nth-child(3) { border-left-color: #D8B4FE !important; }
        main .grid > .kartu[class*="bg-gradient-to-br"]:nth-child(4) { border-left-color: #2DD4BF !important; }
        main .kartu p[class*="text-teal-50"], main .kartu p[class*="text-indigo-50"],
        main .kartu p[class*="text-amber-50"], main .kartu p[class*="text-rose-50"] { color: #C9C4E2 !important; }
        main .kartu .bg-white\/20 { background: rgba(167,139,250,.16) !important; }

        /* Typography: keep headings bright and supporting text muted */
        main h1, main h2, main h3, main h4,
        main .text-ink-900, main .text-slate-800, main .text-slate-900,
        main .text-gray-900, main .text-gray-800 { color: #F2EEFF !important; }
        main .text-slate-500, main .text-slate-600, main .text-gray-500,
        main .text-gray-600, main .text-ink-700 { color: #A7A3C4 !important; }
        main .text-slate-700, main .text-gray-700 { color: #D6D2EA !important; }

        /* Tables */
        main table { color: #EAE6FA !important; border-color: #30294F !important; }
        main thead, main thead tr { background: #1A1C35 !important; color: #DCD5FA !important; }
        main tbody tr { background: #111426 !important; border-color: #292641 !important; }
        main tbody tr:nth-child(even) { background: #15182C !important; }
        main tbody tr:hover { background: #211A3D !important; }
        main td, main th { border-color: #2B2746 !important; }

        /* Form controls */
        main input:not([type="checkbox"]):not([type="radio"]), main select,
        main textarea, main .form-input {
            background: #0D1020 !important; color: #F2EEFF !important;
            border: 1px solid #3A335B !important; border-radius: 10px !important;
        }
        main input::placeholder, main textarea::placeholder { color: #777392 !important; }
        main input:focus, main select:focus, main textarea:focus {
            outline: none !important; border-color: #8B5CF6 !important;
            box-shadow: 0 0 0 3px rgba(139,92,246,.16) !important;
        }
        main option { background: #111426; color: #F2EEFF; }

        /* Buttons and links */
        main button[class*="bg-brand"], main a[class*="bg-brand"],
        main button[class*="bg-indigo-"], main a[class*="bg-indigo-"] {
            background: linear-gradient(110deg, #8B5CF6, #6D28D9) !important;
            color: #fff !important; border-color: #9F7AEA !important;
            box-shadow: 0 6px 16px rgba(109,40,217,.2) !important;
        }
        main button, main a { transition: filter .18s ease, transform .18s ease; }
        main button:hover, main a[class*="rounded"]:hover { filter: brightness(1.12); }
        main .border-slate-200, main .border-gray-200 { border-color: #30294F !important; }
        main .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: #30294F !important; }

        /* Chart areas are tuned for the dark background */
        main canvas { filter: saturate(1.08); }
        @media (max-width: 768px) {
            main { padding: 1rem !important; }
            main .kartu { border-radius: 15px !important; }
        }


        /* ===== COLORFUL LUXURY UPDATE — header beda dari sidebar ===== */
        header {
            background: linear-gradient(110deg, #25205A 0%, #17264A 52%, #31134F 100%) !important;
            border-bottom: 1px solid rgba(167,139,250,.42) !important;
        }
        header h1 { color: #FFFFFF !important; letter-spacing: -.025em; }
        header p { color: #D6C9FF !important; }
        /* Heading setiap halaman punya panel/aksen tersendiri, bukan warna sidebar */
        main h1, main h2, main h3, main h4 { text-shadow: 0 1px 16px rgba(139,92,246,.12); }
        main .dashboard-chart-panel {
            background: linear-gradient(145deg, #17192F 0%, #101426 100%) !important;
            border: 1px solid #39345F !important;
            box-shadow: 0 14px 34px rgba(0,0,0,.24), inset 0 1px 0 rgba(255,255,255,.035) !important;
        }
        main .dashboard-chart-panel > p { color: #EDE9FF !important; }
        /* Statistik cerah, warna berbeda tapi satu gaya visual */
        main .dashboard-stat-grid > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(1) {
            background: linear-gradient(135deg, #8B5CF6 0%, #6428D9 100%) !important;
            border: 1px solid #B79AFF !important; border-left: 4px solid #D8C7FF !important;
            box-shadow: 0 12px 28px rgba(124,58,237,.24) !important;
        }
        main .dashboard-stat-grid > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(2) {
            background: linear-gradient(135deg, #3988F5 0%, #2451C7 100%) !important;
            border: 1px solid #77B5FF !important; border-left: 4px solid #B8DCFF !important;
            box-shadow: 0 12px 28px rgba(37,99,235,.22) !important;
        }
        main .dashboard-stat-grid > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(3) {
            background: linear-gradient(135deg, #F6B94A 0%, #D97706 100%) !important;
            border: 1px solid #FFD58B !important; border-left: 4px solid #FFF0B8 !important;
            box-shadow: 0 12px 28px rgba(217,119,6,.2) !important;
        }
        main .dashboard-stat-grid > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(4) {
            background: linear-gradient(135deg, #F472B6 0%, #C02678 100%) !important;
            border: 1px solid #FDA4D4 !important; border-left: 4px solid #FFE0F0 !important;
            box-shadow: 0 12px 28px rgba(219,39,119,.2) !important;
        }
        main .dashboard-stat-grid.secondary > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(1) {
            background: linear-gradient(135deg, #2DD4BF 0%, #0F8B8D 100%) !important;
            border: 1px solid #7CF4E4 !important; border-left: 4px solid #C9FFF6 !important;
            box-shadow: 0 12px 28px rgba(13,148,136,.2) !important;
        }
        main .dashboard-stat-grid.secondary > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(2) {
            background: linear-gradient(135deg, #FB923C 0%, #C2410C 100%) !important;
            border: 1px solid #FDBA74 !important; border-left: 4px solid #FFEDD5 !important;
            box-shadow: 0 12px 28px rgba(234,88,12,.2) !important;
        }
        main .dashboard-stat-grid.secondary > .kartu.dashboard-stat-card[class*="bg-gradient-to-br"]:nth-child(3) {
            background: linear-gradient(135deg, #A78BFA 0%, #7C3AED 100%) !important;
            border: 1px solid #D8B4FE !important; border-left: 4px solid #F3E8FF !important;
            box-shadow: 0 12px 28px rgba(139,92,246,.2) !important;
        }
        main .dashboard-stat-card { color: #FFFFFF !important; border-radius: 20px !important; }
        main .dashboard-stat-card p { color: #FFFFFF !important; }
        main .dashboard-stat-card .dashboard-stat-icon { background: rgba(255,255,255,.2) !important; box-shadow: inset 0 1px 0 rgba(255,255,255,.2); }
        main .dashboard-stat-card:hover { transform: translateY(-4px) scale(1.008); }
        main .dashboard-chart-panel canvas { max-width: 100%; }
        @media (prefers-reduced-motion: reduce) { main .dashboard-stat-card, main .kartu { transition: none !important; } }


        /* ===== EDUVISION COLORFUL LUXURY v2 =====
           Sidebar = plum gelap; header judul = biru elektrik/ungu; konten = navy.
           Tidak memakai hijau pada latar/kartu utama. */
        :root {
            --ev-bg: #090B18;
            --ev-panel: #17152D;
            --ev-panel-2: #1D2040;
            --ev-line: #393061;
            --ev-purple: #8B5CF6;
            --ev-purple-bright: #C4B5FD;
            --ev-text: #F7F4FF;
            --ev-muted: #B9B3D5;
        }
        body { background: #090B18 !important; color: #F7F4FF !important; }
        /* Vertical sidebar remains dark plum, clearly separate from title bar */
        body > div > aside, .flex.min-h-screen > aside {
            background: linear-gradient(180deg,#21123F 0%,#17102E 52%,#100D22 100%) !important;
            border-right: 2px solid #7C3AED !important;
            box-shadow: 10px 0 32px rgba(0,0,0,.28) !important;
        }
        aside .border-b { border-color: rgba(196,181,253,.20) !important; }
        aside nav a { color: #D8D0F2 !important; }
        aside nav a:hover { background: rgba(167,139,250,.17) !important; color: #FFFFFF !important; }
        aside nav a.bg-brand-600\/20, aside nav a[class*="border-brand-500"] {
            background: linear-gradient(105deg,#A855F7 0%,#7C3AED 52%,#5B21B6 100%) !important;
            border-color: #E9D5FF !important; color: #FFFFFF !important;
            box-shadow: 0 8px 24px rgba(139,92,246,.30) !important;
        }
        aside p.text-slate-500 { color: #A99BCF !important; }
        /* Horizontal page title/header intentionally different: electric blue to violet */
        header {
            background: linear-gradient(105deg,#172554 0%,#3730A3 46%,#6D28D9 100%) !important;
            border-bottom: 1px solid #8B5CF6 !important;
            box-shadow: 0 8px 26px rgba(30,27,75,.30) !important;
            color: #FFFFFF !important;
        }
        header h1 { color: #FFFFFF !important; }
        header p { color: #E0E7FF !important; }
        header .text-teal-200, header .text-indigo-200 { color: #DDD6FE !important; }
        main { background: #0B0E1D !important; color: #F7F4FF !important; }
        footer { background: #100D22 !important; color: #B9B3D5 !important; border-color: #393061 !important; }
        /* Standard panels / charts: dark navy surface, not black */
        main .dashboard-chart-panel, main .bg-white, main [class~="bg-slate-50"],
        main [class~="bg-slate-100"], main [class~="bg-gray-50"], main [class~="bg-gray-100"] {
            background: linear-gradient(145deg,#191B35 0%,#14162B 100%) !important;
            color: #F7F4FF !important; border-color: #393061 !important;
            box-shadow: 0 10px 26px rgba(0,0,0,.22), inset 0 1px 0 rgba(255,255,255,.035) !important;
        }
        main .dashboard-chart-panel { border-width: 1px !important; border-radius: 18px !important; }
        main h1, main h2, main h3, main h4, main .text-ink-900,
        main .text-slate-800, main .text-slate-900, main .text-gray-900, main .text-gray-800 { color: #F7F4FF !important; }
        main .text-slate-500, main .text-slate-600, main .text-gray-500, main .text-gray-600,
        main .text-ink-700 { color: #B9B3D5 !important; }
        main table { color: #F0ECFF !important; border-color: #393061 !important; }
        main thead, main thead tr { background: #242044 !important; color: #E9D5FF !important; }
        main tbody tr { background: #15172C !important; border-color: #30294F !important; }
        main tbody tr:nth-child(even) { background: #1A1B34 !important; }
        main tbody tr:hover { background: #28204A !important; }
        main td, main th { border-color: #393061 !important; }
        main input:not([type="checkbox"]):not([type="radio"]), main select, main textarea, main .form-input {
            background: #111329 !important; color: #F7F4FF !important; border: 1px solid #4A3A70 !important;
        }
        main input:focus, main select:focus, main textarea:focus { border-color: #A78BFA !important; box-shadow: 0 0 0 3px rgba(139,92,246,.2) !important; }
        /* Bright jewel-tone dashboard cards: purple, blue, gold, pink, cyan; no green */
        main .dashboard-stat-card:nth-of-type(1) { background: linear-gradient(135deg,#8B5CF6 0%,#6D28D9 100%) !important; border-color:#C4B5FD !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(2) { background: linear-gradient(135deg,#3B82F6 0%,#1D4ED8 100%) !important; border-color:#93C5FD !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(3) { background: linear-gradient(135deg,#FBBF24 0%,#D97706 100%) !important; border-color:#FDE68A !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(4) { background: linear-gradient(135deg,#F472B6 0%,#DB2777 100%) !important; border-color:#FBCFE8 !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(5) { background: linear-gradient(135deg,#06B6D4 0%,#2563EB 100%) !important; border-color:#A5F3FC !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(6) { background: linear-gradient(135deg,#A855F7 0%,#7C3AED 100%) !important; border-color:#DDD6FE !important; color:#FFFFFF !important; }
        main .dashboard-stat-card:nth-of-type(7) { background: linear-gradient(135deg,#FB7185 0%,#BE185D 100%) !important; border-color:#FECDD3 !important; color:#FFFFFF !important; }
        main .dashboard-stat-card p, main .dashboard-stat-card span { color: #FFFFFF !important; }
        main .dashboard-stat-card .bg-white\/20 { background: rgba(255,255,255,.19) !important; }
        main .kartu::before { background: conic-gradient(from var(--shimmer-angle,0deg),transparent 0%,#A78BFA 10%,#60A5FA 18%,#F472B6 25%,transparent 34%) !important; }
        main button[class*="bg-brand"], main a[class*="bg-brand"], main button[class*="bg-indigo-"], main a[class*="bg-indigo-"] {
            background: linear-gradient(110deg,#8B5CF6,#6D28D9) !important; color:#FFFFFF !important; border-color:#C4B5FD !important;
        }
        @media (max-width: 768px) { header { background: linear-gradient(120deg,#172554,#6D28D9) !important; } }
    
    

/* ===== EDUVISION DARK LUXURY: GLOBAL THEME OVERRIDE ===== */
:root { color-scheme: dark; --ev-bg:#0b1020; --ev-panel:#151a31; --ev-border:#343052; --ev-purple:#8b5cf6; --ev-lavender:#c4b5fd; --ev-blue:#3b82f6; --ev-cyan:#22d3ee; --ev-pink:#f472b6; --ev-gold:#fbbf24; }
html, body { background:#0b1020 !important; color:#e9e7f5; }
body { font-family:'Plus Jakarta Sans',sans-serif; }
/* Sidebar and page heading intentionally use different palettes */
aside { background:linear-gradient(180deg,#211443 0%,#17112f 52%,#0e1022 100%) !important; color:#e9ddff !important; border-right:1px solid #6941c6 !important; }
aside nav a { color:#d8cdef !important; border-radius:14px; transition:background .2s ease, transform .2s ease; }
aside nav a:hover { background:rgba(139,92,246,.18) !important; color:#fff !important; }
aside nav a.bg-brand-600, aside nav a.bg-indigo-600, aside nav a.border-l-4 { background:linear-gradient(100deg,#9b5cff,#6325c8) !important; color:#fff !important; box-shadow:0 8px 22px rgba(124,58,237,.24); }
header { background:linear-gradient(105deg,#17264b 0%,#31205a 58%,#44206a 100%) !important; color:#fff !important; border-bottom:1px solid #5b3b92 !important; }
header h1, header h2, header p, header span { color:#fff; }
main { background:#0b1020 !important; color:#e9e7f5 !important; }
main h1, main h2, main h3, main h4, main label, main th { color:#f1edff; }
main p, main td, main .text-slate-500, main .text-slate-600, main .text-gray-500, main .text-gray-600 { color:#c1bfd6; }
/* Neutral surfaces used across admin, teacher and student pages */
main .bg-white, main .bg-slate-50, main .bg-slate-100, main .bg-gray-50, main .bg-gray-100 { background-color:#171a2d !important; color:#eeeaff !important; }
main .border-slate-100, main .border-slate-200, main .border-gray-100, main .border-gray-200 { border-color:#34324e !important; }
main input:not([type=checkbox]):not([type=radio]), main select, main textarea { background:#11152a !important; color:#f5f1ff !important; border-color:#494064 !important; }
main input::placeholder, main textarea::placeholder { color:#918ba9 !important; }
main table thead { background:#211a3b !important; }
main table tbody tr { border-color:#302b48 !important; }
main table tbody tr:hover { background:rgba(139,92,246,.10) !important; }
/* Buttons and links */
main a.bg-indigo-600, main button.bg-indigo-600, main a.bg-brand-600, main button.bg-brand-600 { background:linear-gradient(100deg,#8b5cf6,#6d28d9) !important; color:white !important; }
main a.bg-teal-600, main button.bg-teal-600, main .bg-teal-600 { background:linear-gradient(100deg,#8b5cf6,#6d28d9) !important; color:white !important; }
main .text-teal-600, main .text-teal-700, main .text-teal-800 { color:#c4b5fd !important; }
main .bg-teal-50, main .bg-teal-100 { background:#211a3b !important; }
/* Colorful cards: bright accents, not the old green palette */
.dashboard-stat-grid .dashboard-stat-card:nth-child(1) { background:linear-gradient(135deg,#8b5cf6,#6325c8) !important; border-color:#b69aff !important; color:#fff !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(2) { background:linear-gradient(135deg,#3b82f6,#4338ca) !important; border-color:#93c5fd !important; color:#fff !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(3) { background:linear-gradient(135deg,#fbbf24,#d97706) !important; border-color:#fde68a !important; color:#21152a !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(4) { background:linear-gradient(135deg,#f472b6,#be185d) !important; border-color:#f9a8d4 !important; color:#fff !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(5) { background:linear-gradient(135deg,#22d3ee,#2563eb) !important; border-color:#a5f3fc !important; color:#10152b !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(6) { background:linear-gradient(135deg,#fb7185,#c026d3) !important; border-color:#fda4af !important; color:#fff !important; }
.dashboard-stat-grid .dashboard-stat-card:nth-child(7) { background:linear-gradient(135deg,#a78bfa,#6366f1) !important; border-color:#c4b5fd !important; color:#fff !important; }
.dashboard-stat-card p, .dashboard-stat-card div { color:inherit; }
.kartu { --shimmer-angle:0deg; }
.kartu::before { background:conic-gradient(from var(--shimmer-angle,0deg),transparent 0%,#a78bfa 10%,#22d3ee 18%,#f472b6 25%,transparent 35%) !important; }
::-webkit-scrollbar-thumb { background:#7650bd !important; }


/* ===== FINAL PALETTE LOCK: NO GREEN / TEAL ===== */
:root { --ev-purple:#8B5CF6; --ev-blue:#3B82F6; --ev-cyan:#22D3EE; --ev-pink:#F472B6; --ev-gold:#FBBF24; }
/* Replace teal/emerald/green utility colors with violet or blue throughout role pages */
[class*="text-teal-"], [class*="text-emerald-"], [class*="text-green--"] { color:#C4B5FD !important; }
[class*="bg-teal-"], [class*="bg-emerald-"], [class*="bg-green-"] { background-color:#6D28D9 !important; color:#fff !important; }
[class*="border-teal-"], [class*="border-emerald-"], [class*="border-green-"] { border-color:#8B5CF6 !important; }
[class*="from-teal-"], [class*="from-emerald-"], [class*="from-green-"] { --tw-gradient-from:#7C3AED var(--tw-gradient-from-position) !important; --tw-gradient-to:rgb(124 58 237 / 0) var(--tw-gradient-to-position) !important; }
[class*="via-teal-"], [class*="via-emerald-"], [class*="via-green-"] { --tw-gradient-stops:var(--tw-gradient-from), #312E81 var(--tw-gradient-via-position), var(--tw-gradient-to) !important; }
[class*="to-teal-"], [class*="to-emerald-"], [class*="to-green-"] { --tw-gradient-to:#1D4ED8 var(--tw-gradient-to-position) !important; }
main .grid > .kartu[class*="bg-gradient-to-br"]:nth-child(4) { border-left-color:#22D3EE !important; }
.kartu::before { background:conic-gradient(from var(--shimmer-angle, 0deg), transparent 0%, #8B5CF6 8%, #22D3EE 16%, #F472B6 24%, transparent 32%) !important; }


/* ===== FIX KONTRAS: kotak/pil berlatar terang di tema gelap ===== */
/* Pil hari (SENIN, SELASA, ...) dan badge pastel indigo/biru/violet */
main .bg-indigo-50, main .bg-indigo-100, main .bg-blue-50, main .bg-blue-100, main .bg-violet-50, main .bg-violet-100, main .bg-purple-50, main .bg-purple-100 { background:linear-gradient(100deg,#7C3AED,#4F46E5) !important; color:#FFFFFF !important; border-color:#A78BFA !important; box-shadow:0 4px 14px rgba(124,58,237,.35); }
main .bg-indigo-50.text-indigo-600, main .bg-indigo-50.text-indigo-700, main .text-indigo-600.bg-indigo-50, main .text-indigo-700.bg-indigo-50 { color:#FFFFFF !important; }
main .bg-indigo-50 *, main .bg-indigo-100 * { color:inherit !important; }
/* Kotak catatan/motivasi di rapor (gradient dari putih ke biru) */
main [class*="from-indigo-50"], main [class*="from-blue-50"], main [class*="from-violet-50"] { --tw-gradient-from:#4C1D95 var(--tw-gradient-from-position) !important; --tw-gradient-to:#1E3A8A var(--tw-gradient-to-position) !important; --tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to) !important; border-color:#7C3AED !important; }
main [class*="from-indigo-50"] p, main [class*="from-indigo-50"] span, main [class*="from-indigo-50"] div { color:#F5F3FF !important; }
/* Kotak info terang lainnya (pengaturan, import, nilai) */
main .border-indigo-100 { border-color:#6D28D9 !important; }
main .text-indigo-700, main .text-indigo-600 { color:#DDD6FE !important; }

/* ===== LOGO EduVision ===== */
.ev-logo { background:linear-gradient(135deg,#F472B6 0%,#8B5CF6 50%,#22D3EE 100%) !important; color:#fff !important; box-shadow:0 6px 20px rgba(139,92,246,.55), inset 0 1px 0 rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.25); text-shadow:0 1px 3px rgba(0,0,0,.35); }
</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        /* ===== Palet baru: "elegant jewel-tone" =====
                           Ini menimpa warna BAWAAN Tailwind (teal/indigo/amber/rose/emerald),
                           jadi SEMUA class lama di seluruh file (bg-teal-600, text-rose-600,
                           dst) otomatis ikut berubah ke palet baru ini tanpa perlu file
                           itu sendiri diedit satu-satu. */
                        teal: { // Electric cyan — tidak ada lagi nuansa hijau
                            50: '#ECFEFF', 100: '#CFFAFE', 200: '#A5F3FC', 300: '#67E8F9',
                            400: '#22D3EE', 500: '#06B6D4', 600: '#0891B2', 700: '#0E7490',
                            800: '#155E75', 900: '#164E63',
                        },
                        indigo: { // Royal violet yang lebih hidup
                            50: '#F5F3FF', 100: '#EDE9FE', 200: '#DDD6FE', 300: '#C4B5FD',
                            400: '#A78BFA', 500: '#8B5CF6', 600: '#7C3AED', 700: '#6D28D9',
                            800: '#5B21B6', 900: '#4C1D95',
                        },
                        amber: { // Gold cerah, bukan cokelat kusam
                            50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D',
                            400: '#FBBF24', 500: '#F59E0B', 600: '#D97706', 700: '#B45309',
                            800: '#92400E', 900: '#78350F',
                        },
                        rose: { // Pink cerah
                            50: '#FFF1F2', 100: '#FFE4E6', 200: '#FECDD3', 300: '#FDA4AF',
                            400: '#FB7185', 500: '#F43F5E', 600: '#E11D48', 700: '#BE123C',
                            800: '#9F1239', 900: '#881337',
                        },
                        emerald: { // Biru langit untuk status sukses — hindari hijau
                            50: '#F0F9FF', 100: '#E0F2FE', 200: '#BAE6FD', 300: '#7DD3FC',
                            400: '#38BDF8', 500: '#0EA5E9', 600: '#0284C7', 700: '#0369A1',
                            800: '#075985', 900: '#0C4A6E',
                        },
                        brand: {
                            50: '#F5F3FF', 100: '#EDE9FE', 400: '#A78BFA',
                            500: '#8B5CF6', 600: '#7C3AED', 700: '#6D28D9',
                            900: '#4C1D95'
                        },
                        ink: { 900: '#2f3646', 800: '#3f4653', 700: '#535b66' },
                        gold: { 400: '#b6a279', 500: '#a98f5d', 600: '#957c4e' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed z-40 inset-y-0 left-0 h-[100dvh] w-72 flex flex-col bg-gradient-to-b from-ink-900 via-teal-900 to-ink-900 text-slate-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen shadow-2xl">
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10 shrink-0">
                <div class="w-10 h-10 rounded-xl ev-logo flex items-center justify-center font-extrabold text-white text-lg shrink-0">E</div>
                <div class="min-w-0">
                    <p class="font-extrabold text-white text-lg leading-none tracking-tight truncate">EduVision</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">Sistem Akademik Sekolah</p>
                </div>
            </div>

            <nav class="px-4 py-5 space-y-1 overflow-y-auto flex-1 min-h-0">
                @php
                    $user = auth()->user();
                    $menu = function($routeName) { return request()->routeIs($routeName) ? 'bg-brand-600/20 text-brand-300 border-brand-500' : 'text-slate-300 border-transparent hover:bg-white/5 hover:text-white'; };
                @endphp

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('dashboard') }} transition">
                    <span>🏠</span><span class="text-sm font-semibold">Dashboard</span>
                </a>

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-slate-500 font-bold">Master Data</p>
                {{-- Data Siswa & Mata Pelajaran: admin bisa kelola penuh, guru bisa LIHAT saja (dibatasi di route/controller) --}}
                <a href="{{ route('siswa.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('siswa.*') }} transition"><span>🎓</span><span class="text-sm font-semibold">Data Siswa</span></a>
                @if($user->isAdmin())
                <a href="{{ route('guru.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('guru.*') }} transition"><span>👨‍🏫</span><span class="text-sm font-semibold">Data Guru</span></a>
                <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('kelas.*') }} transition"><span>🏫</span><span class="text-sm font-semibold">Kelas & Wali</span></a>
                @endif
                <a href="{{ route('mapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('mapel.*') }} transition"><span>📘</span><span class="text-sm font-semibold">Mata Pelajaran</span></a>
                @endif

                @if($user->isAdmin() || $user->isGuru())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-slate-500 font-bold">Akademik</p>
                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.*') }} transition"><span>🗓️</span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                @if($user->isGuru())
                {{-- Materi, Input Nilai, Pengumpulan Tugas, Bank Soal: KHUSUS guru/wali kelas, admin tidak punya akses lagi --}}
                <a href="{{ route('materi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.index') }} transition"><span>📚</span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('nilai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('nilai.*') }} transition"><span>📝</span><span class="text-sm font-semibold">Input Nilai</span></a>
                <a href="{{ route('tugas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.index') }} transition"><span>📥</span><span class="text-sm font-semibold">Pengumpulan Tugas</span></a>
                @endif
                <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.index') }} transition"><span>📷</span><span class="text-sm font-semibold">Rekap Absensi</span></a>
                <a href="{{ route('absensi.monitor') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.monitor') }} transition"><span>🛰️</span><span class="text-sm font-semibold">Monitor Lokasi & Wajah</span></a>
                {{-- Absen QR Per Mapel: KHUSUS guru/wali kelas, admin tidak lagi punya akses. --}}
                @if($user->isGuru())
                <a href="{{ route('absensimapel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.index') }} transition"><span>📶</span><span class="text-sm font-semibold">Absen QR Per Mapel</span></a>
                @endif
                <a href="{{ route('absensimapel.bolos') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.bolos') }} transition"><span>🚩</span><span class="text-sm font-semibold">Deteksi Membolos</span></a>
                <a href="{{ route('prestasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('prestasi.*') }} transition"><span>🏆</span><span class="text-sm font-semibold">Prestasi Siswa</span></a>
                @if($user->isGuru())
                <a href="{{ route('banksoal.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.index') }} transition"><span>❓</span><span class="text-sm font-semibold">Bank Soal</span></a>
                @endif
                <a href="{{ route('aspirasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.index') }} transition"><span>📢</span><span class="text-sm font-semibold">Kotak Aspirasi</span></a>
                @endif

                @if($user->isAdmin())
                <a href="{{ route('spp.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.index') }} transition"><span>💳</span><span class="text-sm font-semibold">SPP</span></a>
                <a href="{{ route('pengaturan.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('pengaturan.*') }} transition"><span>⚙️</span><span class="text-sm font-semibold">Pengaturan Sekolah</span></a>
                @endif

                @if($user->isSiswa())
                <p class="px-4 pt-4 pb-1 text-[11px] uppercase tracking-widest text-slate-500 font-bold">Menu Saya</p>
                <a href="{{ route('absensi.form') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensi.form') }} transition"><span>📷</span><span class="text-sm font-semibold">Absen Sekarang</span></a>
                <a href="{{ route('absensimapel.formManual') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('absensimapel.formManual') }} transition"><span>📶</span><span class="text-sm font-semibold">Absen QR Mapel</span></a>
                <a href="{{ route('jadwal.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('jadwal.saya') }} transition"><span>🗓️</span><span class="text-sm font-semibold">Jadwal Pelajaran</span></a>
                <a href="{{ route('materi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('materi.saya') }} transition"><span>📚</span><span class="text-sm font-semibold">Materi Pembelajaran</span></a>
                <a href="{{ route('tugas.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('tugas.saya') }} transition"><span>📝</span><span class="text-sm font-semibold">Tugas Saya</span></a>
                <a href="{{ route('rapor.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('rapor.saya') }} transition"><span>📄</span><span class="text-sm font-semibold">Rapor Saya</span></a>
                <a href="{{ route('spp.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('spp.saya') }} transition"><span>💳</span><span class="text-sm font-semibold">Status SPP</span></a>
                <a href="{{ route('banksoal.latihan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('banksoal.latihan') }} transition"><span>❓</span><span class="text-sm font-semibold">Latihan Soal</span></a>
                <a href="{{ route('aspirasi.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.create') }} transition"><span>📢</span><span class="text-sm font-semibold">Kirim Aspirasi</span></a>
                <a href="{{ route('aspirasi.saya') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-l-4 {{ $menu('aspirasi.saya') }} transition"><span>📬</span><span class="text-sm font-semibold">Riwayat Aspirasi</span></a>
                @endif

                <div class="pt-4 mt-4 border-t border-white/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-3 px-4 py-2.5 rounded-xl w-full text-left text-rose-300 hover:bg-rose-500/10 transition text-sm font-semibold">
                            <span>🚪</span><span>Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-20 bg-gradient-to-r from-ink-900 via-teal-900 to-ink-900 text-white px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 shrink-0 rounded-lg bg-white/15 text-white flex items-center justify-center text-xl">☰</button>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-xl font-bold text-white leading-snug break-words">@yield('title', 'Dashboard')</h1>
                        <p class="text-[11px] sm:text-xs text-teal-200 font-medium leading-snug break-words">{{ now()->translatedFormat('l, d F Y') }} • EduVision</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-teal-200 capitalize">{{ str_replace('_',' ', auth()->user()->role) }}</p>
                    </div>
                    <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button @click="userMenu = !userMenu"
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white font-bold flex items-center justify-center hover:ring-2 hover:ring-brand-300 transition shrink-0 overflow-hidden">
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

            <footer class="bg-gradient-to-r from-ink-900 via-teal-900 to-ink-900 text-slate-300 text-center text-xs py-4 px-4">
                <p>© {{ date('Y') }} <span class="font-bold text-white">EduVision</span> — Sistem Akademik Sekolah</p>
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
</body>
</html>
