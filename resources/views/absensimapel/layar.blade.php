@extends('layouts.app')
@section('title', 'Layar Absen QR')
@section('content')
<x-tombol-kembali :href="route('absensimapel.index')" />

<div class="grid lg:grid-cols-5 gap-5">
    <div class="lg:col-span-2 bg-gradient-to-br from-ink-900 to-slate-800 rounded-2xl p-6 text-center text-white">
        <p class="text-sm text-slate-300">{{ $sesi->mataPelajaran->nama_mapel }} — {{ $sesi->kelas->nama_kelas }}</p>
        <p class="text-xs text-slate-400 mb-4">Jam ke {{ $sesi->labelJamKe() }} • {{ $sesi->tanggal->translatedFormat('d F Y') }}</p>

        <div id="areaQr" class="bg-white rounded-2xl p-4 inline-block mx-auto"></div>

        <p class="text-xs text-slate-400 mt-4">QR berganti otomatis tiap beberapa detik</p>
        <div class="mt-1 text-3xl font-black tabular-nums" id="countdown">--</div>

        <div class="mt-4 bg-white/10 rounded-xl p-3">
            <p class="text-[11px] text-slate-300 mb-1">Atau siswa bisa masukkan kode ini secara manual:</p>
            <p class="text-2xl font-black tracking-[0.3em]" id="kodeManual">--------</p>
        </div>

        <form method="POST" action="{{ route('absensimapel.tutup', $sesi) }}" class="mt-5" onsubmit="return confirm('Tutup sesi absen ini? Siswa yang belum absen otomatis ditandai Tanpa Keterangan.')">
            @csrf
            <button class="w-full bg-rose-600 hover:bg-rose-700 transition rounded-xl px-6 py-3 text-sm font-bold">🔒 Tutup Absen</button>
        </form>
    </div>

    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
        <div class="flex flex-wrap gap-3 mb-4" id="ringkasan">
            <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">🟢 Hadir: <span id="rHadir">0</span></span>
            <span class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">⚪ Belum: <span id="rBelum">0</span></span>
            <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">🟡 Izin/Sakit: <span id="rIzinSakit">0</span></span>
            <span class="px-3 py-1.5 rounded-full bg-rose-100 text-rose-700 text-xs font-bold">🔴 Tanpa Keterangan: <span id="rTanpaKet">0</span></span>
        </div>
        <div id="daftarSiswa" class="grid sm:grid-cols-2 gap-2 max-h-[520px] overflow-y-auto pr-1"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
const urlToken = "{{ route('absensimapel.token', $sesi) }}";
const urlStatus = "{{ route('absensimapel.status', $sesi) }}";
const urlManualBase = "{{ url('/absen-mapel/'.$sesi->id.'/manual') }}";
const csrfToken = "{{ csrf_token() }}";

let sisaDetik = 0;
let sesiDitutup = false;

function renderQr(url) {
    const holder = document.getElementById('areaQr');
    holder.innerHTML = '';
    const qr = qrcode(0, 'M');
    qr.addData(url);
    qr.make();
    holder.innerHTML = qr.createImgTag(6, 6);
}

async function ambilTokenBaru() {
    if (sesiDitutup) return;
    try {
        const res = await fetch(urlToken);
        const data = await res.json();
        if (data.ditutup) {
            sesiDitutup = true;
            document.getElementById('areaQr').innerHTML = '<p class="text-slate-800 font-bold py-10 px-4">Sesi sudah ditutup</p>';
            document.getElementById('countdown').innerText = '--';
            document.getElementById('kodeManual').innerText = '--------';
            return;
        }
        renderQr(data.url_konfirmasi);
        document.getElementById('kodeManual').innerText = data.token;
        sisaDetik = data.sisa_detik;
        document.getElementById('countdown').innerText = sisaDetik;
    } catch (e) { /* koneksi sempat putus, biarkan siklus berikut mencoba lagi */ }
}

function tickCountdown() {
    if (sesiDitutup) return;
    sisaDetik -= 1;
    if (sisaDetik <= 0) {
        ambilTokenBaru();
    } else {
        document.getElementById('countdown').innerText = sisaDetik;
    }
}

async function ubahManual(siswaId, status) {
    await fetch(`${urlManualBase}/${siswaId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status }),
    });
    ambilStatus();
}

async function ambilStatus() {
    try {
        const res = await fetch(urlStatus);
        const data = await res.json();

        const warna = { 'Hadir': 'bg-emerald-50 border-emerald-200', 'Belum Absen': 'bg-slate-50 border-slate-200', 'Izin': 'bg-amber-50 border-amber-200', 'Sakit': 'bg-amber-50 border-amber-200', 'Tanpa Keterangan': 'bg-rose-50 border-rose-200' };
        const titik = { 'Hadir': '🟢', 'Belum Absen': '⚪', 'Izin': '🟡', 'Sakit': '🟡', 'Tanpa Keterangan': '🔴' };

        document.getElementById('daftarSiswa').innerHTML = data.siswa.map(s => `
            <div class="flex items-center justify-between gap-2 border rounded-xl px-3 py-2 ${warna[s.status] || 'bg-slate-50 border-slate-200'}">
                <span class="text-sm font-semibold text-ink-900 truncate">${titik[s.status] || '⚪'} ${s.nama}</span>
                <select onchange="ubahManual(${s.id}, this.value)" class="text-xs rounded-lg border-2 border-slate-300 px-1.5 py-1 bg-white">
                    <option value="">Ubah...</option>
                    <option value="Hadir" ${s.status === 'Hadir' ? 'selected' : ''}>Hadir</option>
                    <option value="Izin" ${s.status === 'Izin' ? 'selected' : ''}>Izin</option>
                    <option value="Sakit" ${s.status === 'Sakit' ? 'selected' : ''}>Sakit</option>
                    <option value="Tanpa Keterangan" ${s.status === 'Tanpa Keterangan' ? 'selected' : ''}>Tanpa Keterangan</option>
                </select>
            </div>
        `).join('');

        document.getElementById('rHadir').innerText = data.ringkasan.hadir;
        document.getElementById('rBelum').innerText = data.ringkasan.belum;
        document.getElementById('rIzinSakit').innerText = data.ringkasan.izin_sakit;
        document.getElementById('rTanpaKet').innerText = data.ringkasan.tanpa_keterangan;
    } catch (e) { /* koneksi sempat putus, biarkan siklus berikut mencoba lagi */ }
}

ambilTokenBaru();
ambilStatus();
setInterval(tickCountdown, 1000);
setInterval(ambilStatus, 3000);
</script>
@endsection
