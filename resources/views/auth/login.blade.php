<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - EduVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.tema')
    <style>
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); filter: brightness(1.05); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-teal-600 via-teal-700 to-teal-900 p-4">
    <div class="w-full max-w-4xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="hidden md:flex flex-col justify-between bg-gradient-to-br from-teal-600 via-teal-700 to-teal-900 p-10 text-white">
            <a href="{{ url('/') }}" class="flex items-center gap-3 w-fit hover:opacity-80 transition">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-300 to-brand-500 flex items-center justify-center font-extrabold text-xl text-ink-900">E</div>
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
