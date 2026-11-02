@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php $user = auth()->user(); $adm = $user->isAdmin(); @endphp

@if($user->isAdmin() || $user->isGuru())
@if($user->isGuru())
<div class="mb-6">
    <div class="kartu bg-gradient-to-r from-teal-500 to-teal-700 text-white rounded-2xl p-5 sm:p-6 shadow-lg shadow-teal-600/25 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl font-bold shrink-0 overflow-hidden">
            @if($user->fotoUrl())
                <img src="{{ $user->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                     @click="fotoModalUrl = '{{ $user->fotoUrl() }}'" alt="Foto profil">
            @else
                {{ strtoupper(substr($user->guru->nama ?? $user->name, 0, 1)) }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-700 font-semibold uppercase tracking-wide">Halo,</p>
            <p class="text-lg sm:text-xl font-extrabold truncate">{{ $user->guru->nama ?? $user->name }}</p>
        </div>
        <p class="text-sm text-slate-700 sm:text-right shrink-0">{{ $user->guru->nip ?? '' }}</p>
    </div>
</div>
@endif
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('siswa.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-teal-700/25">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3"><x-ikon nama="graduation" class="w-5 h-5" /></div>
        <p class="text-xs text-teal-50 font-semibold uppercase tracking-wide">Total Siswa</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalSiswa }}</p>
    </a>
    <a href="{{ $adm ? route('guru.index') : route('mapel.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl p-5 shadow-lg shadow-indigo-600/25">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3"><x-ikon nama="presentation" class="w-5 h-5" /></div>
        <p class="text-xs text-indigo-50 font-semibold uppercase tracking-wide">Total Guru</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalGuru }}</p>
    </a>
    <a href="{{ $adm ? route('kelas.index') : route('jadwal.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-brand-600 to-brand-700 text-white rounded-2xl p-5 shadow-lg shadow-brand-600/25">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3"><x-ikon nama="users" class="w-5 h-5" /></div>
        <p class="text-xs text-amber-50 font-semibold uppercase tracking-wide">Jumlah Kelas</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalKelas }}</p>
    </a>
    <a href="{{ route('mapel.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-600/25">
        <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3"><x-ikon nama="book" class="w-5 h-5" /></div>
        <p class="text-xs text-rose-50 font-semibold uppercase tracking-wide">Mata Pelajaran</p>
        <p class="text-3xl font-extrabold mt-1">{{ $totalMapel }}</p>
    </a>
</div>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('absensi.index', ['tanggal' => now()->toDateString()]) }}" class="kartu block bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-teal-700/25">
        <p class="text-sm font-semibold opacity-90">Absen Hari Ini (disetujui)</p>
        <p class="text-3xl font-extrabold mt-1">{{ $absenHariIni }}</p>
        @if(($absenMenunggu ?? 0) > 0)
            <p class="mt-2 inline-block text-xs font-bold bg-amber-400 text-ink-900 rounded-full px-2.5 py-1"><x-ikon nama="clock" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ $absenMenunggu }} menunggu persetujuan</p>
        @endif
    </a>
    <a href="{{ $adm ? route('spp.index', ['status' => 'Belum Lunas']) : route('siswa.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-brand-500 to-brand-600 text-white rounded-2xl p-5 shadow-lg shadow-brand-600/25">
        <p class="text-sm font-semibold opacity-90">SPP Belum Lunas</p>
        <p class="text-3xl font-extrabold mt-1">{{ $sppBelumLunas }}</p>
    </a>
    <a href="{{ route('aspirasi.index') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-600/25">
        <p class="text-sm font-semibold opacity-90">Aspirasi Baru</p>
        <p class="text-3xl font-extrabold mt-1">{{ $aspirasiBaru }}</p>
    </a>
</div>

<!-- ===== GRAFIK-GRAFIK DASHBOARD ===== -->
<div class="grid lg:grid-cols-2 gap-5 mb-5">
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="trending-up" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Tren Kehadiran 7 Hari Terakhir</p>
        <canvas id="chartKehadiran" height="180"></canvas>
    </div>
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="users" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Jumlah Siswa per Kelas</p>
        <canvas id="chartKelas" height="180"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="credit-card" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Status Pembayaran SPP</p>
        <canvas id="chartSpp" height="180"></canvas>
    </div>
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="bar-chart" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Rata-rata Nilai per Mata Pelajaran</p>
        <canvas id="chartMapel" height="180"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Palet baru — senada dengan warna elegan di seluruh aplikasi (bukan warna default Chart.js yang norak)
    const palet = ['#5f86c4', '#d9a521', '#82a4d6', '#f28275', '#8cb97f', '#6f93cb', '#efc23f', '#f3b698', '#aac3e6', '#f0bb3a'];
    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#737e96';

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
                borderColor: '#5f86c4',
                backgroundColor: (c) => gradientVertikal(c.chart.ctx, 'rgba(19,126,111,0.35)', 'rgba(19,126,111,0.02)'),
                fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: '#d9a521', pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 3,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f7f9fd' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('chartKelas'), {
        type: 'bar',
        data: {
            labels: @json($grafikKelasLabel),
            datasets: [{ label: 'Jumlah Siswa', data: @json($grafikKelasData), backgroundColor: palet, borderRadius: 10, maxBarThickness: 46 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f7f9fd' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('chartSpp'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Lunas'],
            datasets: [{ data: [{{ $grafikSppLunas }}, {{ $grafikSppBelum }}], backgroundColor: ['#8cb97f', '#f28275'], borderWidth: 3, borderColor: '#fff' }]
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
                x: { beginAtZero: true, max: 100, grid: { color: '#f7f9fd' } },
                y: { grid: { display: false }, ticks: { font: { weight: '600' } } }
            }
        }
    });
});
</script>
@endif

@if($user->isSiswa() && isset($profilSiswa))
<div class="mb-4">
    <div class="kartu bg-gradient-to-r from-teal-500 to-teal-700 text-white rounded-2xl p-5 sm:p-6 shadow-lg shadow-teal-600/25 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl font-bold shrink-0 overflow-hidden">
            @if($user->fotoUrl())
                <img src="{{ $user->fotoUrl() }}" class="w-full h-full object-cover cursor-zoom-in"
                     @click="fotoModalUrl = '{{ $user->fotoUrl() }}'" alt="Foto profil">
            @else
                {{ strtoupper(substr($profilSiswa->nama, 0, 1)) }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-700 font-semibold uppercase tracking-wide">Halo,</p>
            <p class="text-lg sm:text-xl font-extrabold truncate">{{ $profilSiswa->nama }}</p>
        </div>
        <p class="text-sm text-slate-700 sm:text-right shrink-0">{{ $profilSiswa->kelas->nama_kelas ?? '-' }}</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4 mb-6">
    <a href="{{ route('rapor.saya') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-6 shadow-lg shadow-teal-700/25">
        <p class="text-sm font-semibold opacity-90">Rata-rata Nilai</p>
        <p class="text-4xl font-extrabold mt-1">{{ $rataRata }}</p>
    </a>
    <a href="{{ route('absensi.form') }}" class="kartu block hover:-translate-y-0.5 transition bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-6 shadow-lg shadow-indigo-600/25">
        <p class="text-sm font-semibold opacity-90">Persentase Kehadiran</p>
        <p class="text-4xl font-extrabold mt-1">{{ $kehadiran }}%</p>
    </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('absensi.form') }}" class="kartu bg-gradient-to-br from-teal-500 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-teal-700/25 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2"><x-ikon nama="scan-face" class="w-5 h-5" /></div>
        <p class="font-bold">Absen Sekarang</p>
    </a>
    <a href="{{ route('rapor.saya') }}" class="kartu bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl p-5 shadow-lg shadow-indigo-600/25 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2"><x-ikon nama="file-text" class="w-5 h-5" /></div>
        <p class="font-bold">Lihat Rapor</p>
    </a>
    <a href="{{ route('banksoal.latihan') }}" class="kartu bg-gradient-to-br from-brand-600 to-brand-700 text-white rounded-2xl p-5 shadow-lg shadow-brand-600/25 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2"><x-ikon nama="help" class="w-5 h-5" /></div>
        <p class="font-bold">Latihan Soal</p>
    </a>
    <a href="{{ route('spp.saya') }}" class="kartu bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-2xl p-5 shadow-lg shadow-rose-600/25 transition">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-lg mb-2"><x-ikon nama="credit-card" class="w-5 h-5" /></div>
        <p class="font-bold">Status SPP</p>
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="bar-chart" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Nilai per Mata Pelajaran</p>
        <canvas id="chartNilaiSiswa" height="200"></canvas>
    </div>
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="calendar" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Rekap Kehadiran</p>
        <canvas id="chartAbsenSiswa" height="200"></canvas>
    </div>
</div>

{{-- grid-cols-2 (bukan grid-cols-1): supaya diagram ini tidak memenuhi/terpusat
     di seluruh lebar halaman, dan ada ruang kosong di sampingnya (kanan) untuk
     diagram lain yang mau ditambahkan belakangan. --}}
<div class="grid lg:grid-cols-2 gap-5 mt-5">
    <div class="kartu bg-white rounded-2xl p-5 shadow-sm border-2 border-slate-200">
        <p class="font-bold text-ink-900 mb-3"><x-ikon nama="trending-up" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Tren Nilai per Semester</p>
        <canvas id="chartSemesterSiswa" height="140"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#737e96';

    // Diagram nilai per mapel siswa — sebelumnya radar (susah dibaca kalau
    // mapelnya sedikit/nilainya berdekatan), diganti bar horizontal per mapel
    // dengan warna berbeda tiap mapel, supaya lebih jelas dan gampang dibandingkan.
    const paletSiswa = ['#5f86c4', '#d9a521', '#82a4d6', '#f28275', '#8cb97f', '#6f93cb', '#efc23f', '#f3b698', '#aac3e6', '#f0bb3a'];
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
                x: { beginAtZero: true, max: 100, grid: { color: '#f7f9fd' } },
                y: { grid: { display: false }, ticks: { font: { weight: '600' } } }
            }
        }
    });

    new Chart(document.getElementById('chartAbsenSiswa'), {
        type: 'pie',
        data: {
            labels: @json($grafikAbsenLabel),
            datasets: [{ data: @json($grafikAbsenData), backgroundColor: ['#8cb97f', '#efc23f', '#82a4d6', '#f28275'], borderWidth: 3, borderColor: '#fff' }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } } }
    });

    // Tren nilai per semester — digabung BAR berwarna beda tiap semester (biar
    // langsung kelihatan jelas angka tiap semester), tanpa garis tren.
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
            ]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Rata-rata: ' + ctx.parsed.y } }
            },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: '#f7f9fd' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endif
@endsection
