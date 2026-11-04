{{-- Cek notifikasi baru tiap 30 detik: toast + bunyi "ting" lembut (WebAudio, tanpa file). --}}
<div id="toast-notif" class="fixed right-4 top-20 z-[60] hidden max-w-xs rounded-2xl border-2 border-teal-300 bg-white px-4 py-3 shadow-xl">
    <p id="toast-judul" class="text-sm font-bold text-ink-900"></p>
    <p id="toast-isi" class="text-xs text-slate-500 mt-0.5"></p>
    <a href="{{ route('notifikasi.index') }}" class="mt-1 inline-block text-xs font-bold text-teal-700 hover:underline">Lihat notifikasi</a>
</div>
<script>
(function () {
    var ctx = null, kunciLS = 'edu_notif_terakhir_{{ auth()->id() }}';
    function buka() {
        try {
            ctx = ctx || new (window.AudioContext || window.webkitAudioContext)();
            if (ctx.state === 'suspended') { ctx.resume(); }
        } catch (e) {}
    }
    ['click', 'keydown', 'touchstart'].forEach(function (ev) { document.addEventListener(ev, buka, { once: true, passive: true }); });

    function ting() {
        if (!ctx || ctx.state !== 'running') { return; }
        [[880, 0], [1320, 0.16]].forEach(function (n) {
            var o = ctx.createOscillator(), g = ctx.createGain(), t = ctx.currentTime + n[1];
            o.type = 'sine'; o.frequency.value = n[0];
            g.gain.setValueAtTime(0.0001, t);
            g.gain.exponentialRampToValueAtTime(0.18, t + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, t + 0.5);
            o.connect(g); g.connect(ctx.destination); o.start(t); o.stop(t + 0.55);
        });
    }
    function toast(d) {
        var el = document.getElementById('toast-notif'); if (!el) { return; }
        document.getElementById('toast-judul').textContent = d.judul || 'Notifikasi baru';
        document.getElementById('toast-isi').textContent = d.isi || '';
        el.classList.remove('hidden');
        setTimeout(function () { el.classList.add('hidden'); }, 7000);
    }
    function cek() {
        if (document.hidden) { return; }
        fetch('{{ route('notifikasi.cek') }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) {
                if (!d) { return; }
                var terakhir = 0;
                try { terakhir = parseInt(localStorage.getItem(kunciLS) || '0', 10); } catch (e) {}
                if (d.id_terbaru > terakhir) {
                    if (d.jumlah > 0) { ting(); toast(d); }
                    try { localStorage.setItem(kunciLS, String(d.id_terbaru)); } catch (e) {}
                }
                document.querySelectorAll('[data-badge-notif]').forEach(function (b) { b.textContent = d.jumlah > 9 ? '9+' : d.jumlah; });
            }).catch(function () {});
    }
    setTimeout(cek, 1500);
    setInterval(cek, 60000);
})();
</script>
