@extends('layouts.app')
@section('title', 'Absen Sekarang')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-lg mx-auto text-center kartu">
    @if($sudahAbsen && $absensiHariIni && $absensiHariIni->status_persetujuan === 'menunggu')
        <div class="py-10">
            <span class="text-5xl">⏳</span>
            <p class="mt-4 font-bold text-lg text-ink-900">Absenmu menunggu persetujuan</p>
            <p class="text-slate-500 text-sm mt-1">Absen sudah terkirim sebagai <b>{{ $absensiHariIni->status }}</b>, tapi baru dihitung setelah admin atau guru menyetujuinya.</p>
            @if($absensiHariIni->alasan_menunggu)
                <p class="mt-3 text-xs rounded-xl bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 text-left">{{ $absensiHariIni->alasan_menunggu }}</p>
            @endif
        </div>
    @elseif($sudahAbsen)
        <div class="py-10">
            <span class="text-5xl">✅</span>
            <p class="mt-4 font-bold text-lg text-ink-900">Kamu sudah absen hari ini</p>
            <p class="text-slate-500 text-sm">Absenmu sudah disetujui. Sampai jumpa besok!</p>
        </div>
    @else
        @if($absensiDitolak)
            <div class="mb-4 text-left rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
                <p class="font-bold">Absen sebelumnya ditolak.</p>
                <p class="text-xs mt-0.5">{{ $absensiDitolak->catatan_persetujuan ?: 'Tidak ada catatan dari admin/guru.' }} Kirim ulang absenmu di bawah ini.</p>
            </div>
        @endif
        <p class="text-sm text-slate-500 mb-4">Posisikan wajahmu di depan kamera, lalu tekan tombol Absen.</p>
        <div class="rounded-2xl overflow-hidden bg-slate-900 aspect-video flex items-center justify-center relative">
            <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="hidden"></canvas>
        </div>
        <p class="text-xs text-slate-400 mt-3">
            Catatan: sistem ini merekam foto wajah + lokasi sebagai bukti kehadiran dasar.
            Jika kamu memilih Hadir dan lokasimu di dalam radius {{ $sekolah['radius_meter'] }} meter dari sekolah, absensi langsung disetujui.
            Jika kamu tidak hadir (Izin/Sakit/Lainnya) atau berada di luar radius, absen tetap bisa dikirim tapi harus menunggu persetujuan admin atau guru.
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
            <input type="hidden" name="akurasi" id="akurasi">
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
    // HARUS sama dengan Absensi::MAKS_KOMPENSASI_AKURASI di server.
    const MAKS_KOMPENSASI = {{ \App\Models\Absensi::MAKS_KOMPENSASI_AKURASI }};

    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) ** 2 + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
    // Rumus yang sama persis dengan Absensi::jarakEfektif() di server: jarak GPS mentah
    // dikurangi toleransi akurasi GPS, minimal 0. Berdiri di titik sekolah = 0 meter.
    function jarakEfektif(mentah, akurasi) {
        return Math.max(0, mentah - Math.min(Math.max(akurasi || 0, 0), MAKS_KOMPENSASI));
    }

    // Ambil titik GPS TERBAIK: pantau beberapa detik dengan akurasi tinggi, simpan fix
    // dengan akurasi paling kecil (bukan fix pertama yang sering kasar/cache lama).
    let fixTerbaik = null, watchId = null;
    function tampilkanLokasi() {
        const badge = document.getElementById('statusLokasi');
        if (!fixTerbaik) return;
        const mentah = hitungJarakMeter(sekolahLat, sekolahLng, fixTerbaik.latitude, fixTerbaik.longitude);
        const jarak = Math.round(jarakEfektif(mentah, fixTerbaik.accuracy));
        const info = ' (akurasi GPS ± ' + Math.round(fixTerbaik.accuracy) + ' m)';
        if (jarak <= radius) {
            badge.className = 'mt-3 text-xs font-semibold rounded-xl px-3 py-2 bg-emerald-50 text-emerald-700';
            badge.innerText = '✅ Kamu terdeteksi di area sekolah — jarak ' + jarak + ' meter' + info;
        } else {
            badge.className = 'mt-3 text-xs font-semibold rounded-xl px-3 py-2 bg-amber-50 text-amber-700';
            badge.innerText = '⚠️ Kamu ' + jarak + ' meter dari sekolah' + info + ' — absen tetap bisa dikirim, tapi harus menunggu persetujuan admin/guru';
        }
    }
    function simpanFix(pos) {
        if (!fixTerbaik || pos.coords.accuracy <= fixTerbaik.accuracy) {
            fixTerbaik = { latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy };
            document.getElementById('latitude').value = fixTerbaik.latitude;
            document.getElementById('longitude').value = fixTerbaik.longitude;
            document.getElementById('akurasi').value = fixTerbaik.accuracy;
            tampilkanLokasi();
        }
        if (fixTerbaik.accuracy <= 15 && watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
    }

    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(simpanFix, () => {
            if (!fixTerbaik) document.getElementById('statusLokasi').innerText = '⚠️ Tidak bisa mendeteksi lokasi. Pastikan izin GPS aktif — absen tetap bisa dikirim dan menunggu persetujuan admin/guru.';
        }, { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 });
        setTimeout(() => { if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; } }, 15000);
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
