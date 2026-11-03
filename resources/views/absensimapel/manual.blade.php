@extends('layouts.app')
@section('title', 'Absen QR Mapel')
@section('content')
<div class="max-w-md mx-auto mt-6">
    <div class="bg-gradient-to-br from-ink-900 to-slate-800 rounded-2xl p-6 text-white text-center mb-4">
        <span class="text-4xl">🔑</span>
        <h2 class="mt-2 font-extrabold text-lg">Input Kode Absen Mapel</h2>
        <p class="text-xs text-slate-300 mt-1">Dipakai kalau kamera tidak bisa scan QR. Minta guru menunjukkan kode 8 karakter yang tampil di layar depan kelas.</p>
    </div>

    @if(session('gagal'))
        <div class="mb-4 rounded-xl bg-rose-50 border-2 border-rose-200 text-rose-700 px-4 py-3 text-sm font-semibold flex items-start gap-2">
            <span class="text-base leading-none">⚠️</span>
            <span>{{ session('gagal') }}</span>
        </div>
    @endif
    @if(session('sukses'))
        <div class="mb-4 rounded-xl bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-semibold flex items-start gap-2">
            <span class="text-base leading-none">✅</span>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu" x-data="kodeAbsen()">
        <label class="block text-xs font-semibold text-slate-500 mb-3 text-center">Masukkan 8 karakter kode di bawah ini</label>

        <form method="POST" action="{{ route('absensimapel.prosesManual') }}" @submit="return kodeLengkap()">
            @csrf
            <input type="hidden" name="kode" x-ref="hidden" :value="kotak.join('')">

            <div class="flex justify-center gap-1.5 sm:gap-2" @paste.prevent="tempel($event)">
                @for($i = 0; $i < 8; $i++)
                    <input type="text" maxlength="1" inputmode="text" autocomplete="off"
                        x-ref="kotak{{ $i }}"
                        @input="isi($event, {{ $i }})"
                        @keydown.backspace="mundur($event, {{ $i }})"
                        @keydown.arrow-left="fokus({{ $i - 1 }})"
                        @keydown.arrow-right="fokus({{ $i + 1 }})"
                        {{ $i === 0 ? 'autofocus' : '' }}
                        class="w-9 h-11 sm:w-10 sm:h-12 text-center uppercase font-black text-lg rounded-xl border-2 border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                @endfor
            </div>

            <button type="submit"
                class="mt-6 w-full bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition tombol-aktif disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="!kodeLengkap()">
                Kirim Absen
            </button>
        </form>

        <div class="mt-5 border-t border-slate-100 pt-4 space-y-1.5">
            <p class="text-xs text-slate-400">⏱️ Kodenya berganti otomatis tiap beberapa detik — kalau ditolak, minta guru menampilkan kode terbaru lalu coba lagi.</p>
            <p class="text-xs text-slate-400">🔒 Jangan bagikan kode ini ke siswa lain. Kode ini dipakai untuk mencatat KEHADIRANMU sendiri, bukan milik orang lain.</p>
        </div>
    </div>
</div>

<script>
function kodeAbsen() {
    return {
        kotak: Array(8).fill(''),
        isi(e, i) {
            let v = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(-1);
            e.target.value = v;
            this.kotak[i] = v;
            if (v && i < 7) this.fokus(i + 1);
        },
        mundur(e, i) {
            if (e.target.value === '' && i > 0) {
                this.fokus(i - 1);
                this.$nextTick(() => { this.$refs['kotak' + (i - 1)].value = ''; this.kotak[i - 1] = ''; });
            }
        },
        fokus(i) {
            if (i >= 0 && i <= 7) this.$refs['kotak' + i].focus();
        },
        tempel(e) {
            const teks = (e.clipboardData.getData('text') || '').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8);
            for (let i = 0; i < 8; i++) {
                const c = teks[i] || '';
                this.kotak[i] = c;
                this.$refs['kotak' + i].value = c;
            }
            this.fokus(Math.min(teks.length, 7));
        },
        kodeLengkap() {
            return this.kotak.join('').length === 8;
        },
    };
}
</script>
@endsection
