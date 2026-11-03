@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

@if($user->isAdmin() || $user->isGuru())
@if($user->isGuru())
<div class="mb-6">
    <div class="kartu bg-slate-500/90 text-white rounded-2xl p-5 sm:p-6 shadow-md shadow-slate-900/10 border border-slate-400/30 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl font-bold shrink-0 overflow-hidden">
            @if($user->fotoUrl())
                <img src="{{ $user->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                     @click="fotoModalUrl = '{{ $user->fotoUrl() }}'" alt="Foto profil">
            @else
                {{ strtoupper(substr($user->guru->nama ?? $user->name, 0, 1)) }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-200 font-semibold uppercase tracking-wide">Halo,</p>
            <p class="text-lg sm:text-xl font-extrabold truncate">{{ $user->guru->nama ?? $user->name }}</p>
        </div>
        <p class="text-sm text-slate-200 sm:text-right shrink-0">{{ $user->guru->nip ?? '' }}</p>
    </div>
</div>
@endif
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 dashboard-stat-grid">
    <div class="kartu bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-teal-900/20 border-2 border-teal-400/30 dashboard-stat-card">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">🎓</div>
        <p class="text-xs text-teal-50 font-semibold uppercase tracking-wide">Total Siswa</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalSiswa }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl p-5 shadow-lg shadow-indigo-900/20 border-2 border-indigo-400/30 dashboard-stat-card">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">👨‍🏫</div>
        <p class="text-xs text-indigo-50 font-semibold uppercase tracking-wide">Total Guru</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalGuru }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-amber-500 to-amber-700 text-white rounded-2xl p-5 shadow-lg shadow-amber-900/20 border-2 border-amber-400/30 dashboard-stat-card">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">🏫</div>
        <p class="text-xs text-amber-50 font-semibold uppercase tracking-wide">Jumlah Kelas</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalKelas }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-900/20 border-2 border-rose-400/30 dashboard-stat-card">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">📘</div>
        <p class="text-xs text-rose-50 font-semibold uppercase tracking-wide">Mata Pelajaran</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalMapel }}</p>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4 mb-6 dashboard-stat-grid secondary">
    <div class="kartu bg-gradient-to-br from-teal-500 to-teal-800 text-white rounded-2xl p-5 shadow-lg shadow-teal-900/20 dashboard-stat-card">
        <p class="text-sm font-semibold opacity-90">Absen Hari Ini</p>
        <p class="text-3xl font-extrabold mt-1">{{ $absenHariIni }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-amber-400 to-amber-700 text-white rounded-2xl p-5 shadow-lg shadow-amber-900/20 dashboard-stat-card">
        <p class="text-sm font-semibold opacity-90">SPP Belum Lunas</p>
        <p class="text-3xl font-extrabold mt-1">{{ $sppBelumLunas }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-rose-500 to-rose-800 text-white rounded-2xl p-5 shadow-lg shadow-rose-900/20 dashboard-stat-card">
        <p class="text-sm font-semibold opacity-90">Aspirasi Baru</p>
        <p class="text-3xl font-extrabold mt-1">{{ $aspirasiBaru }}</p>
    </div>
</div>

<!-- ===== GRAFIK-GRAFIK DASHBOARD ===== -->
<div class="grid lg:grid-cols-2 gap-5 mb-5">
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">📈 Tren Kehadiran 7 Hari Terakhir</p>
        <canvas id="chartKehadiran" height="180"></canvas>
    </div>
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">🏫 Jumlah Siswa per Kelas</p>
        <canvas id="chartKelas" height="180"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">💳 Status Pembayaran SPP</p>
        <canvas id="chartSpp" height="180"></canvas>
    </div>
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">📊 Rata-rata Nilai per Mata Pelajaran</p>
        <canvas id="chartMapel" height="180"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Palet baru — senada dengan warna elegan di seluruh aplikasi (bukan warna default Chart.js yang norak)
    const palet = ['#A78BFA', '#60A5FA', '#FBBF24', '#F472B6', '#22D3EE', '#818CF8', '#FB923C', '#E879F9', '#93C5FD', '#F9A8D4'];
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#B8B3D5';

    function gradientVertikal(ctx, warnaAtas, warnaBawah) {
        const g = ctx.createLinearGradient(0, 0, 0, 220);
        g.addColorStop(0, warnaAtas); g.addColorStop(1, warnaBawah);
        return g;
    }

    new Chart(document.getElementById('chartKehadiran'), {
        type: 'line',
        data: {
            labels: @json($grafikKehadiranLabel),
            datasets: [{
                label: 'Siswa Hadir',
                data: @json($grafikKehadiranData),
                borderColor: '#A78BFA',
                backgroundColor: (c) => gradientVertikal(c.chart.ctx, 'rgba(139,92,246,0.32)', 'rgba(139,92,246,0.015)'),
                fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: '#C4B5FD', pointBorderColor: '#151528', pointBorderWidth: 2, borderWidth: 3,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(167,139,250,0.13)' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('chartKelas'), {
        type: 'bar',
        data: {
            labels: @json($grafikKelasLabel),
            datasets: [{ label: 'Jumlah Siswa', data: @json($grafikKelasData), backgroundColor: palet, borderRadius: 10, maxBarThickness: 46 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(167,139,250,0.13)' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('chartSpp'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Lunas'],
            datasets: [{ data: [{{ $grafikSppLunas }}, {{ $grafikSppBelum }}], backgroundColor: ['#A78BFA', '#F472B6'], borderWidth: 3, borderColor: '#151528' }]
        },
        options: { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } } }
    });

    // Diagram nilai per mapel — sebelumnya satu warna flat & polos, sekarang tiap
    // mapel dapat warna sendiri dari palet + sumbu dirapikan supaya lebih jelas dibaca.
    new Chart(document.getElementById('chartMapel'), {
        type: 'bar',
        data: {
            labels: @json($grafikMapelLabel),
            datasets: [{
                label: 'Rata-rata Nilai',
                data: @json($grafikMapelData),
                backgroundColor: palet,
                borderRadius: 10,
                maxBarThickness: 28,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Rata-rata: ' + ctx.parsed.x } }
            },
            scales: {
                x: { beginAtZero: true, max: 100, grid: { color: 'rgba(167,139,250,0.13)' } },
                y: { grid: { display: false }, ticks: { font: { weight: '600' } } }
            }
        }
    });
});
</script>
@endif

@if($user->isSiswa() && isset($profilSiswa))
<div class="mb-4">
    <div class="kartu bg-slate-500/90 text-white rounded-2xl p-5 sm:p-6 shadow-md shadow-slate-900/10 border border-slate-400/30 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl font-bold shrink-0 overflow-hidden">
            @if($user->fotoUrl())
                <img src="{{ $user->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                     @click="fotoModalUrl = '{{ $user->fotoUrl() }}'" alt="Foto profil">
            @else
                {{ strtoupper(substr($profilSiswa->nama, 0, 1)) }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-200 font-semibold uppercase tracking-wide">Halo,</p>
            <p class="text-lg sm:text-xl font-extrabold truncate">{{ $profilSiswa->nama }}</p>
        </div>
        <p class="text-sm text-slate-200 sm:text-right shrink-0">{{ $profilSiswa->kelas->nama_kelas ?? '-' }}</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4 mb-6">
    <div class="kartu bg-gradient-to-br from-teal-500 to-teal-800 text-white rounded-2xl p-6 shadow-lg shadow-teal-900/20">
        <p class="text-sm font-semibold opacity-90">Rata-rata Nilai</p>
        <p class="text-4xl font-extrabold mt-1">{{ $rataRata }}</p>
    </div>
    <div class="kartu bg-gradient-to-br from-indigo-500 to-indigo-800 text-white rounded-2xl p-6 shadow-lg shadow-indigo-900/20">
        <p class="text-sm font-semibold opacity-90">Persentase Kehadiran</p>
        <p class="text-4xl font-extrabold mt-1">{{ $kehadiran }}%</p>
    </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('absensi.form') }}" class="kartu bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-teal-900/20 border-2 border-teal-400/30 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2">📷</div>
        <p class="font-bold">Absen Sekarang</p>
    </a>
    <a href="{{ route('rapor.saya') }}" class="kartu bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl p-5 shadow-lg shadow-indigo-900/20 border-2 border-indigo-400/30 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2">📄</div>
        <p class="font-bold">Lihat Rapor</p>
    </a>
    <a href="{{ route('banksoal.latihan') }}" class="kartu bg-gradient-to-br from-amber-500 to-amber-700 text-white rounded-2xl p-5 shadow-lg shadow-amber-900/20 border-2 border-amber-400/30 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2">❓</div>
        <p class="font-bold">Latihan Soal</p>
    </a>
    <a href="{{ route('spp.saya') }}" class="kartu bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-900/20 border-2 border-rose-400/30 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2">💳</div>
        <p class="font-bold">Status SPP</p>
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">📊 Nilai per Mata Pelajaran</p>
        <canvas id="chartNilaiSiswa" height="200"></canvas>
    </div>
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3">🗓️ Rekap Kehadiran</p>
        <canvas id="chartAbsenSiswa" height="200"></canvas>
    </div>
</div>

{{-- grid-cols-2 (bukan grid-cols-1): supaya diagram ini tidak memenuhi/terpusat
     di seluruh lebar halaman, dan ada ruang kosong di sampingnya (kanan) untuk
     diagram lain yang mau ditambahkan belakangan. --}}
<div class="grid lg:grid-cols-2 gap-5 mt-5">
    <div class="kartu dashboard-chart-panel bg-white rounded-2xl p-5 shadow-sm border-2 border-indigo-200">
        <p class="font-bold text-ink-900 mb-3">📈 Tren Nilai per Semester</p>
        <canvas id="chartSemesterSiswa" height="140"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#B8B3D5';

    // Diagram nilai per mapel siswa — sebelumnya radar (susah dibaca kalau
    // mapelnya sedikit/nilainya berdekatan), diganti bar horizontal per mapel
    // dengan warna berbeda tiap mapel, supaya lebih jelas dan gampang dibandingkan.
    const paletSiswa = ['#A78BFA', '#60A5FA', '#FBBF24', '#F472B6', '#22D3EE', '#818CF8', '#FB923C', '#E879F9', '#93C5FD', '#F9A8D4'];
    new Chart(document.getElementById('chartNilaiSiswa'), {
        type: 'bar',
        data: {
            labels: @json($grafikNilaiLabel),
            datasets: [{
                label: 'Nilai Akhir',
                data: @json($grafikNilaiData),
                backgroundColor: paletSiswa,
                borderRadius: 8,
                maxBarThickness: 28,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Nilai: ' + ctx.parsed.x } }
            },
            scales: {
                x: { beginAtZero: true, max: 100, grid: { color: 'rgba(167,139,250,0.13)' } },
                y: { grid: { display: false }, ticks: { font: { weight: '600' } } }
            }
        }
    });

    new Chart(document.getElementById('chartAbsenSiswa'), {
        type: 'pie',
        data: {
            labels: @json($grafikAbsenLabel),
            datasets: [{ data: @json($grafikAbsenData), backgroundColor: ['#569d7f', '#b6a279', '#8272a7', '#955d6f'], borderWidth: 3, borderColor: '#17192F' }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } } }
    });

    // Tren nilai per semester — digabung BAR berwarna beda tiap semester (biar
    // langsung kelihatan jelas angka tiap semester) + garis tren di atasnya
    // (biar kelihatan naik/turunnya), jadi lebih jelas & berwarna dibanding
    // sebelumnya yang cuma satu garis polos satu warna.
    new Chart(document.getElementById('chartSemesterSiswa'), {
        data: {
            labels: @json($grafikSemesterLabel),
            datasets: [
                {
                    type: 'bar',
                    label: 'Rata-rata Nilai',
                    data: @json($grafikSemesterData),
                    backgroundColor: paletSiswa,
                    borderRadius: 10,
                    maxBarThickness: 56,
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Tren',
                    data: @json($grafikSemesterData),
                    borderColor: '#6b5b92',
                    backgroundColor: '#6b5b92',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 6,
                    pointBackgroundColor: '#6b5b92',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    order: 1,
                },
            ]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Rata-rata: ' + ctx.parsed.y } }
            },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: 'rgba(167,139,250,0.13)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endif
@endsection
