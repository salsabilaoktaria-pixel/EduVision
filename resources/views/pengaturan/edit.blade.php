@extends('layouts.app')
@section('title', 'Pengaturan Sekolah')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="kartu bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl">
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-5 text-xs text-indigo-700">
        <p class="font-bold mb-1"><x-ikon nama="map-pin" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Kenapa ini penting?</p>
        <p>Titik koordinat di sini dipakai untuk menghitung jarak siswa saat absen Face ID — kalau siswa memilih Hadir dan jaraknya masih di dalam radius, absen otomatis disetujui. Selain itu (tidak hadir / di luar radius) absen menunggu persetujuan admin/guru. Sebelumnya nilainya cuma contoh/placeholder, isi dengan koordinat sekolah Anda yang SESUNGGUHNYA di sini.</p>
    </div>

    @if(session('sukses'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><x-ikon nama="check-circle" class="w-4 h-4 inline-block align-[-2px] mr-1" /> {{ session('sukses') }}</div>
    @endif

    <form method="POST" action="{{ route('pengaturan.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <h3 class="font-bold text-ink-900">Identitas Sekolah</h3>
        <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-2xl border-2 border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                @if($pengaturan->logoUrl())<img src="{{ $pengaturan->logoUrl() }}" class="w-full h-full object-contain p-1" alt="Logo sekolah">@else<x-ikon nama="school" class="w-9 h-9 text-slate-400" />@endif
            </div>
            <div class="min-w-0">
                <label class="text-sm font-semibold text-slate-700">Logo Sekolah <span class="text-slate-400 font-normal">(dipakai di kop rapor)</span></label>
                <input type="file" name="logo" accept="image/png,image/jpeg" class="mt-1 block w-full text-xs text-slate-600">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Nama Sekolah</label>
                <input type="text" name="nama" value="{{ old('nama', $pengaturan->nama) }}" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none">
            </div>
            <div><label class="text-sm font-semibold text-slate-700">NPSN</label>
                <input type="text" name="npsn" value="{{ old('npsn', $pengaturan->npsn) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Jenjang</label>
                <select name="jenjang" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none">
                    @foreach(['SD','SMP','SMA','SMK','MA'] as $j)<option value="{{ $j }}" @selected(old('jenjang', $pengaturan->jenjang) === $j)>{{ $j }}</option>@endforeach
                </select></div>
            <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat', $pengaturan->alamat) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Kota / Kabupaten</label>
                <input type="text" name="kota" value="{{ old('kota', $pengaturan->kota) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Email Sekolah</label>
                <input type="email" name="email" value="{{ old('email', $pengaturan->email) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">Kepala Sekolah</label>
                <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah', $pengaturan->kepala_sekolah) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
            <div><label class="text-sm font-semibold text-slate-700">NIP Kepala Sekolah</label>
                <input type="text" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $pengaturan->nip_kepala_sekolah) }}" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm outline-none"></div>
        </div>

        <h3 class="font-bold text-ink-900 pt-2">Lokasi Absen Face ID</h3>
        <button type="button" id="btnLokasiSaya" class="tombol-aktif w-full bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-xl px-5 py-3 text-sm font-bold hover:from-indigo-600 hover:to-indigo-800 transition shadow-lg shadow-indigo-600/20">
            <x-ikon nama="map-pin" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Pakai Lokasi Saya Sekarang (harus sedang berada di sekolah)
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

        <button class="tombol-aktif w-full bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition"><x-ikon nama="check" class="w-4 h-4" /> Simpan Pengaturan</button>
    </form>
</div>

<script>
// Titik sekolah diambil dengan GPS AKURASI TINGGI selama beberapa detik dan memakai fix
// terbaik — supaya titik yang tersimpan setara kualitasnya dengan titik GPS siswa.
// (Sebelumnya memakai fix pertama yang kasar, jadi siswa yang berdiri di tempat yang sama
// bisa terbaca puluhan meter dari sekolah.)
document.getElementById('btnLokasiSaya').addEventListener('click', () => {
    const status = document.getElementById('statusLokasiSaya');
    if (!navigator.geolocation) {
        status.innerText = ' Browser ini tidak mendukung deteksi lokasi.';
        return;
    }
    status.innerText = ' Mendeteksi lokasi dengan akurasi tinggi, tunggu beberapa detik...';
    let terbaik = null, watchId = null;
    const selesai = () => {
        if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        if (terbaik) status.innerText = ' Lokasi terdeteksi (akurasi ± ' + Math.round(terbaik.accuracy) + ' meter). Klik Simpan untuk menerapkan.';
        else status.innerText = ' Gagal mendeteksi lokasi. Pastikan izin GPS diaktifkan di browser.';
    };
    watchId = navigator.geolocation.watchPosition(pos => {
        if (!terbaik || pos.coords.accuracy <= terbaik.accuracy) {
            terbaik = { latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy };
            document.getElementById('latitude').value = terbaik.latitude.toFixed(7);
            document.getElementById('longitude').value = terbaik.longitude.toFixed(7);
            status.innerText = ' Akurasi saat ini ± ' + Math.round(terbaik.accuracy) + ' meter...';
        }
        if (terbaik.accuracy <= 10) selesai();
    }, selesai, { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 });
    setTimeout(selesai, 12000);
});
</script>
@endsection
