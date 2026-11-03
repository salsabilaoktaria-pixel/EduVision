@extends('layouts.app')
@section('title', 'Absen Sekarang')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-lg mx-auto text-center kartu">
    @if($sudahAbsen)
        <div class="py-10">
            <span class="text-5xl">✅</span>
            <p class="mt-4 font-bold text-lg text-ink-900">Kamu sudah absen hari ini</p>
            <p class="text-slate-500 text-sm">Sampai jumpa besok!</p>
        </div>
    @else
        <p class="text-sm text-slate-500 mb-4">Posisikan wajahmu di depan kamera, lalu tekan tombol Absen.</p>
        <div class="rounded-2xl overflow-hidden bg-slate-900 aspect-video flex items-center justify-center relative">
            <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="hidden"></canvas>
        </div>
        <p class="text-xs text-slate-400 mt-3">
            Catatan: sistem ini merekam foto wajah + lokasi sebagai bukti kehadiran dasar.
            Jika lokasimu di dalam radius {{ $sekolah['radius_meter'] }} meter dari sekolah, absensi otomatis terverifikasi.
            Di luar itu, data tetap tersimpan tapi menunggu tinjauan manual wali kelas/admin.
        </p>

        <div id="statusLokasi" class="mt-3 text-xs font-semibold rounded-xl px-3 py-2 bg-slate-50 text-slate-500">📍 Mendeteksi lokasi kamu...</div>

        <div class="mt-4 text-left">
            <label class="text-sm font-semibold text-slate-700">Keterangan <span class="text-rose-500">*</span></label>
            <select id="keterangan" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Keterangan --</option>
                <option value="Hadir">Hadir — datang ke sekolah</option>
                <option value="Izin">Izin — tidak masuk sekolah dengan keterangan</option>
                <option value="Sakit">Sakit — tidak masuk karena sakit</option>
                <option value="Lainnya">Lainnya — tulis sendiri keterangannya</option>
            </select>
            <p id="errorKeterangan" class="hidden text-xs text-rose-500 mt-1">Keterangan wajib dipilih sebelum mengirim absensi.</p>

            <div id="wrapKeteranganLainnya" class="hidden mt-2">
                <input type="text" id="keteranganLainnya" maxlength="255" placeholder="Tulis keterangannya di sini..."
                       class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <p id="errorKeteranganLainnya" class="hidden text-xs text-rose-500 mt-1">Tuliskan dulu keterangannya sebelum mengirim absensi.</p>
            </div>
        </div>

        <form id="formAbsen" method="POST" action="{{ route('absensi.simpan') }}" class="mt-4">
            @csrf
            <input type="hidden" name="foto_base64" id="foto_base64">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="keterangan" id="keteranganHidden">
            <input type="hidden" name="keterangan_detail" id="keteranganDetailHidden">
            <button type="button" id="btnAmbil" class="w-full bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">📸 Ambil Foto & Kirim Absensi</button>
        </form>
    @endif
</div>

@if(!$sudahAbsen)
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    document.getElementById('keterangan').addEventListener('change', function () {
        const wrap = document.getElementById('wrapKeteranganLainnya');
        wrap.classList.toggle('hidden', this.value !== 'Lainnya');
    });

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(() => alert('Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan.'));

    const sekolahLat = {{ $sekolah['latitude'] }};
    const sekolahLng = {{ $sekolah['longitude'] }};
    const radius = {{ $sekolah['radius_meter'] }};

    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) ** 2 + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            document.getElementById('latitude').value = pos.coords.latitude;
            document.getElementById('longitude').value = pos.coords.longitude;

            const jarak = Math.round(hitungJarakMeter(sekolahLat, sekolahLng, pos.coords.latitude, pos.coords.longitude));
            const badge = document.getElementById('statusLokasi');
            if (jarak <= radius) {
                badge.className = 'mt-3 text-xs font-semibold rounded-xl px-3 py-2 bg-emerald-50 text-emerald-700';
                badge.innerText = '✅ Kamu terdeteksi di area sekolah (± ' + jarak + ' meter)';
            } else {
                badge.className = 'mt-3 text-xs font-semibold rounded-xl px-3 py-2 bg-amber-50 text-amber-700';
                badge.innerText = '⚠️ Kamu terdeteksi ' + jarak + ' meter dari sekolah — absen tetap bisa dikirim, tapi akan menunggu verifikasi manual';
            }
        }, () => {
            document.getElementById('statusLokasi').innerText = '⚠️ Tidak bisa mendeteksi lokasi. Pastikan izin GPS aktif.';
        });
    }

    document.getElementById('btnAmbil').addEventListener('click', () => {
        const keterangan = document.getElementById('keterangan').value;
        const errorEl = document.getElementById('errorKeterangan');

        if (!keterangan) {
            errorEl.classList.remove('hidden');
            document.getElementById('keterangan').focus();
            return; // batalkan pengiriman, keterangan wajib diisi dulu
        }
        errorEl.classList.add('hidden');

        if (keterangan === 'Lainnya') {
            const lainnya = document.getElementById('keteranganLainnya').value.trim();
            const errorLainnya = document.getElementById('errorKeteranganLainnya');
            if (!lainnya) {
                errorLainnya.classList.remove('hidden');
                document.getElementById('keteranganLainnya').focus();
                return; // batalkan pengiriman, keterangan manual wajib diisi dulu
            }
            errorLainnya.classList.add('hidden');
            document.getElementById('keteranganDetailHidden').value = lainnya;
        }

        document.getElementById('keteranganHidden').value = keterangan;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        document.getElementById('foto_base64').value = canvas.toDataURL('image/png');
        document.getElementById('formAbsen').submit();
    });
</script>
@endif
@endsection
