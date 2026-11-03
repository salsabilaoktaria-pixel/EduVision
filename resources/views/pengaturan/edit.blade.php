@extends('layouts.app')
@section('title', 'Pengaturan Sekolah')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl">
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-5 text-xs text-indigo-700">
        <p class="font-bold mb-1">📍 Kenapa ini penting?</p>
        <p>Titik koordinat di sini dipakai untuk menghitung jarak siswa saat absen Face ID — kalau jaraknya masih di dalam radius, absen otomatis terverifikasi "di area sekolah". Sebelumnya nilainya cuma contoh/placeholder, isi dengan koordinat sekolah Anda yang SESUNGGUHNYA di sini.</p>
    </div>

    @if(session('sukses'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">✅ {{ session('sukses') }}</div>
    @endif

    <form method="POST" action="{{ route('pengaturan.update') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-700">Nama Sekolah</label>
            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $pengaturan->nama_sekolah) }}" required
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
        </div>

        <button type="button" id="btnLokasiSaya" class="tombol-aktif w-full bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-xl px-5 py-3 text-sm font-bold hover:from-indigo-600 hover:to-indigo-800 transition shadow-lg shadow-indigo-600/20">
            📍 Pakai Lokasi Saya Sekarang (harus sedang berada di sekolah)
        </button>
        <p id="statusLokasiSaya" class="text-xs text-slate-400 text-center"></p>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Latitude</label>
                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $pengaturan->latitude) }}" required
                       class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Longitude</label>
                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $pengaturan->longitude) }}" required
                       class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
        </div>
        <p class="text-xs text-slate-400 -mt-2">Alternatif manual: buka Google Maps, klik kanan di titik lokasi sekolah, koordinatnya akan muncul dan bisa disalin ke sini.</p>

        <div>
            <label class="text-sm font-semibold text-slate-700">Radius Toleransi (meter)</label>
            <input type="number" name="radius_meter" value="{{ old('radius_meter', $pengaturan->radius_meter) }}" required min="20" max="5000"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">Jarak maksimal dari titik sekolah yang masih dianggap "di area sekolah" (area sekolah biasanya 100–300 meter).</p>
        </div>

        <button class="tombol-aktif w-full bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition">Simpan Lokasi Sekolah</button>
    </form>
</div>

<script>
document.getElementById('btnLokasiSaya').addEventListener('click', () => {
    const status = document.getElementById('statusLokasiSaya');
    if (!navigator.geolocation) {
        status.innerText = '⚠️ Browser ini tidak mendukung deteksi lokasi.';
        return;
    }
    status.innerText = '📡 Mendeteksi lokasi...';
    navigator.geolocation.getCurrentPosition(pos => {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        status.innerText = '✅ Lokasi terdeteksi (akurasi ± ' + Math.round(pos.coords.accuracy) + ' meter). Klik Simpan untuk menerapkan.';
    }, () => {
        status.innerText = '⚠️ Gagal mendeteksi lokasi. Pastikan izin GPS diaktifkan di browser.';
    });
});
</script>
@endsection
