<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - EduVision</title>
    @include('partials.tema-sekolah')
    <style>
        .tombol-aktif { transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
        .tombol-aktif:hover { transform: translateY(-2px); filter: brightness(1.05); }
    </style>
    @include('partials.css-tailwind')
</head>
<body class="min-h-screen flex items-center justify-center hero-pastel p-4">
    <div class="w-full max-w-4xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="hidden md:flex flex-col justify-between hero-pastel p-10 text-white">
            <div class="flex flex-col gap-5 my-auto">
                <a href="{{ url('/') }}" class="flex items-center gap-3 w-fit hover:opacity-80 transition">
                    <x-logo class="w-16 h-16" />
                    <span class="text-xl font-extrabold tracking-tight">EduVision</span>
                </a>
                <div>
                    <h2 class="text-3xl font-extrabold leading-tight mb-3">Satu Platform,<br>Seluruh Kegiatan Sekolah.</h2>
                    <p class="text-teal-100 text-sm leading-relaxed">Nilai, absensi, SPP, rapor, bank soal, hingga kotak aspirasi — semua terhubung dalam satu sistem akademik yang rapi dan transparan.</p>
                </div>
            </div>
            <p class="text-xs text-teal-200">&copy; {{ date('Y') }} EduVision</p>
        </div>

        <div class="p-8 sm:p-12 flex flex-col justify-center">
            <div class="md:hidden mb-6 pb-5 border-b-2 border-slate-100">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 w-fit">
                    <x-logo class="w-12 h-12" />
                    <span class="text-lg font-extrabold tracking-tight text-ink-900">EduVision</span>
                </a>
                <h2 class="text-xl font-extrabold leading-tight text-ink-900 mt-3">Satu Platform, Seluruh Kegiatan Sekolah.</h2>
                <p class="text-xs text-slate-500 leading-relaxed mt-1.5">Nilai, absensi, SPP, rapor, bank soal, hingga kotak aspirasi dalam satu sistem.</p>
            </div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-teal-600 transition mb-5 w-fit">
                <x-ikon nama="arrow-left" class="w-4 h-4 inline-block align-[-2px]" /> Kembali ke Beranda
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Selamat Datang </h1>
            <p class="text-slate-500 text-sm mb-6">Masuk ke akun EduVision kamu.</p>

            @if(session('gagal'))
                <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 text-sm font-medium"><x-ikon nama="alert" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ session('gagal') }}</div>
            @endif
            @if(session('sukses'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><x-ikon nama="check-circle" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ session('sukses') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium">
                    @foreach($errors->all() as $error)<p><x-ikon nama="alert" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ $error }}</p>@endforeach
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
