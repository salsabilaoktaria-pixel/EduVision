<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - EduVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;}
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); filter: brightness(1.05); }
    
/* ===== LOGIN: matching dark-luxury palette ===== */
body { background:radial-gradient(ellipse at 18% 15%,#44206f 0%,transparent 42%),linear-gradient(135deg,#0b1020 0%,#17264b 52%,#24113f 100%) !important; color:#f3efff; }
body > div.w-full.max-w-4xl { background:#171a2d !important; border:1px solid #4b3675; box-shadow:0 28px 80px rgba(0,0,0,.42) !important; }
body > div.w-full.max-w-4xl > div:first-child { background:linear-gradient(145deg,#452078 0%,#25205a 52%,#14284b 100%) !important; }
body > div.w-full.max-w-4xl > div:first-child .text-teal-100, body > div.w-full.max-w-4xl > div:first-child .text-teal-200 { color:#ddd6fe !important; }
body > div.w-full.max-w-4xl > div:first-child .from-amber-400 { --tw-gradient-from:#c4b5fd var(--tw-gradient-from-position) !important; --tw-gradient-to:rgb(196 181 253 / 0) var(--tw-gradient-to-position) !important; }
body > div.w-full.max-w-4xl > div:last-child { background:linear-gradient(160deg,#f4f0ff,#e7eaff) !important; color:#17152d !important; }
body > div.w-full.max-w-4xl > div:last-child h1, body > div.w-full.max-w-4xl > div:last-child h2, body > div.w-full.max-w-4xl > div:last-child label { color:#17152d !important; }
body > div.w-full.max-w-4xl input { background:#fff !important; border:1px solid #c4b5fd !important; color:#21183b !important; border-radius:14px !important; }
body > div.w-full.max-w-4xl button[type=submit] { background:linear-gradient(100deg,#8b5cf6,#5b21b6) !important; color:#fff !important; box-shadow:0 10px 22px rgba(109,40,217,.24) !important; }
body > div.w-full.max-w-4xl a { color:#6d28d9 !important; }


/* FINAL COLOR OVERRIDE — remove all green/teal from login */
body { background:radial-gradient(ellipse at 18% 15%,#44206f 0%,transparent 42%),linear-gradient(135deg,#0b1020 0%,#17264b 52%,#24113f 100%) !important; }
body > div.w-full.max-w-4xl > div:first-child { background:linear-gradient(145deg,#452078 0%,#25205a 52%,#14284b 100%) !important; }
body > div.w-full.max-w-4xl > div:last-child { background:linear-gradient(160deg,#f4f0ff,#e7eaff) !important; }
body [class*="teal"], body [class*="emerald"], body [class*="green-"] { --tw-gradient-from:#8B5CF6 var(--tw-gradient-from-position) !important; --tw-gradient-to:rgb(139 92 246 / 0) var(--tw-gradient-to-position) !important; }
body .text-teal-100, body .text-teal-200, body .text-teal-600, body .text-teal-700 { color:#DDD6FE !important; }
body .bg-teal-100, body .bg-teal-500, body .bg-teal-600, body .bg-teal-700 { background:linear-gradient(100deg,#8B5CF6,#5B21B6) !important; color:#fff !important; }
body button[type=submit] { background:linear-gradient(100deg,#8B5CF6,#5B21B6) !important; }


/* LOGO EduVision */
.ev-logo { background:linear-gradient(135deg,#F472B6 0%,#8B5CF6 50%,#22D3EE 100%) !important; color:#fff !important; box-shadow:0 6px 20px rgba(139,92,246,.55), inset 0 1px 0 rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.25); text-shadow:0 1px 3px rgba(0,0,0,.35); }
</style>
    <script>
        // Palet yang SAMA PERSIS dengan homepage & dashboard, supaya warnanya konsisten di semua halaman.
        tailwind.config = { theme: { extend: { colors: {
            ink: { 900:'#2f3646', 800:'#3f4653', 700:'#535b66' },
            teal: { 50:'#f4f9f9',100:'#e1efec',200:'#c4ded9',300:'#9ec7c1',400:'#7aada4',500:'#50958b',600:'#437f76',700:'#3b6e67',800:'#38615a',900:'#325752' },
            amber: { 50:'#f9f7f3',100:'#eee9dc',200:'#dbd0b6',300:'#c7b792',400:'#b6a279',500:'#a98f5d',600:'#957c4e',700:'#7f6944',800:'#705d3e',900:'#64543a' },
        } } } }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-ink-900 via-teal-900 to-ink-900 p-4">
    <div class="w-full max-w-4xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="hidden md:flex flex-col justify-between bg-gradient-to-br from-teal-700 via-teal-900 to-ink-900 p-10 text-white">
            <a href="{{ url('/') }}" class="flex items-center gap-3 w-fit hover:opacity-80 transition">
                <div class="w-11 h-11 rounded-xl ev-logo flex items-center justify-center font-extrabold text-xl text-ink-900">E</div>
                <span class="text-xl font-extrabold tracking-tight">EduVision</span>
            </a>
            <div>
                <h2 class="text-3xl font-extrabold leading-tight mb-3">Satu Platform,<br>Seluruh Kegiatan Sekolah.</h2>
                <p class="text-teal-100 text-sm leading-relaxed">Nilai, absensi, SPP, rapor, bank soal, hingga kotak aspirasi — semua terhubung dalam satu sistem akademik yang rapi dan transparan.</p>
            </div>
            <p class="text-xs text-teal-200">&copy; {{ date('Y') }} EduVision</p>
        </div>

        <div class="p-8 sm:p-12 flex flex-col justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-teal-600 transition mb-5 w-fit">
                ← Kembali ke Beranda
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Selamat Datang 👋</h1>
            <p class="text-slate-500 text-sm mb-6">Masuk ke akun EduVision kamu.</p>

            @if(session('gagal'))
                <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 text-sm font-medium">⚠️ {{ session('gagal') }}</div>
            @endif
            @if(session('sukses'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">✅ {{ session('sukses') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium">
                    @foreach($errors->all() as $error)<p>⚠️ {{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4" autocomplete="off">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="off" spellcheck="false"
                        class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Password</label>
                    <input type="password" name="password" required
                        autocomplete="new-password"
                        class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                </div>
                <button type="submit"
                    class="tombol-aktif w-full bg-gradient-to-r from-teal-500 to-teal-700 text-white font-bold py-3 rounded-xl hover:from-teal-600 hover:to-teal-800 transition shadow-lg shadow-teal-600/30">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
