{{-- Pop-up untuk tombol aksi (Detail, Edit, Tambah): isi halaman yang sama dimuat ke dalam jendela tanpa pindah halaman.
     Tautan dengan atribut data-popup otomatis memakainya. Setelah berhasil menyimpan/menghapus, isi halaman di belakang
     diperbarui secara halus (tanpa muat ulang penuh), jadi seluruh alurnya terasa seragam untuk semua peran. --}}
<style>
    #popup-aksi { position: fixed; inset: 0; z-index: 70; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(41,59,92,.28); -webkit-backdrop-filter: blur(3px); backdrop-filter: blur(3px); }
    #popup-aksi.buka { display: flex; animation: popup-latar .2s ease-out; }
    #popup-aksi.buka .popup-kotak { animation: popup-masuk .22s cubic-bezier(.2,.8,.3,1); }
    @keyframes popup-latar { from { opacity: 0; } to { opacity: 1; } }
    @keyframes popup-masuk { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: none; } }
    #popup-aksi .popup-kotak { width: 100%; max-width: 58rem; max-height: 92vh; display: flex; flex-direction: column; background: #fffdf8; border: 2px solid #b8c8df; border-radius: 1.25rem; box-shadow: 0 24px 50px -20px rgba(41,59,92,.45); overflow: hidden; }
    #popup-aksi .popup-kepala { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .8rem 1.1rem .8rem 1.4rem; border-bottom: 2px solid #dbe4f1; background: #eef3fb; }
    #popup-aksi .popup-judul { font-weight: 800; color: #1f3a5f; font-size: 1.05rem; min-width: 0; overflow-wrap: anywhere; }
    #popup-aksi .popup-tutup { flex: none; display: inline-flex; align-items: center; justify-content: center; width: 2.1rem; height: 2.1rem; border-radius: .7rem; border: 1.5px solid #b8c8df; background: #fffdf8; color: #34507c; cursor: pointer; transition: background-color .15s; }
    #popup-aksi .popup-tutup:hover { background: #e4ecf8; }
    #popup-aksi .popup-isi { padding: 1.25rem 1.4rem 1.4rem; overflow-y: auto; overflow-x: hidden; flex: 1 1 auto; }
    #popup-aksi .popup-muat { text-align: center; color: #64748b; padding: 2.5rem 0; font-weight: 600; }
    /* Di dalam pop-up: tombol kembali disembunyikan (ada tombol X), tombol lain di sebelahnya tetap tampil */
    #popup-aksi .tombol-kembali-bar > a:first-child { display: none; }
    #popup-aksi .tombol-kembali-bar { justify-content: flex-end; margin-bottom: 1rem; }
    #popup-aksi .tombol-kembali-bar:not(:has(> div)) { display: none; }
    /* Kartu tunggal di dalam pop-up memenuhi lebar & tidak lagi berbingkai ganda, jadi tidak ada ruang kosong di sisi/atas */
    #popup-aksi [data-judul] > * { max-width: none !important; margin-left: 0 !important; margin-right: 0 !important; }
    #popup-aksi [data-judul] > :only-child { border: 0 !important; box-shadow: none !important; background: transparent !important; padding: 0 !important; border-radius: 0 !important; }
    #popup-aksi [data-judul] > :not(:last-child) { margin-bottom: 1rem; }
    #popup-aksi .kartu { transform: none !important; }
    #popup-aksi .kilau-ring, #popup-aksi .kartu::after { display: none; }
    #popup-aksi form .grid, #popup-aksi form .space-y-4, #popup-aksi form .space-y-5 { max-width: none; }
    body.popup-terbuka { overflow: hidden; }
    main.sedang-ganti { opacity: .35; transition: opacity .15s; pointer-events: none; }
    main { transition: opacity .2s; }
</style>
<div id="popup-aksi" role="dialog" aria-modal="true" aria-labelledby="popup-judul">
    <div class="popup-kotak">
        <div class="popup-kepala">
            <h3 class="popup-judul" id="popup-judul">Memuat…</h3>
            <button type="button" class="popup-tutup" id="popup-tutup" aria-label="Tutup"><x-ikon nama="x" class="w-5 h-5" /></button>
        </div>
        <div class="popup-isi" id="popup-isi"></div>
    </div>
</div>
<script>
(function () {
    var kotak = document.getElementById('popup-aksi'), isi = document.getElementById('popup-isi'), judul = document.getElementById('popup-judul');
    var urlSaatIni = null, urlAwal = null;
    var hdrModal = { 'X-Modal': '1', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' };
    var hdrBiasa = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' };

    function jalankanScript(wadah) {
        wadah.querySelectorAll('script').forEach(function (lama) {
            var baru = document.createElement('script');
            for (var i = 0; i < lama.attributes.length; i++) baru.setAttribute(lama.attributes[i].name, lama.attributes[i].value);
            baru.textContent = lama.textContent; lama.parentNode.replaceChild(baru, lama);
        });
    }
    function bungkusTabel(wadah) {
        wadah.querySelectorAll('table').forEach(function (t) {
            if (t.closest('.overflow-x-auto')) return;
            var w = document.createElement('div'); w.className = 'overflow-x-auto max-w-full';
            t.parentNode.insertBefore(w, t); w.appendChild(t);
        });
    }
    function tutup() { kotak.classList.remove('buka'); document.body.classList.remove('popup-terbuka'); isi.innerHTML = ''; urlSaatIni = null; urlAwal = null; }
    function tampil(html, url) {
        urlSaatIni = url; isi.innerHTML = html;
        var pembungkus = isi.querySelector('[data-judul]');
        judul.textContent = (pembungkus && pembungkus.dataset.judul) || 'Detail';
        jalankanScript(isi); bungkusTabel(isi); isi.scrollTop = 0;
    }
    function sama(a, b) { return !!a && !!b && a.split('#')[0] === b.split('#')[0]; }
    function hdrPopup() { return Object.assign({}, hdrModal, { 'X-Modal-Asal': urlAwal || '', 'X-Modal-Sekarang': urlSaatIni || '' }); }
    function htmlOk(res) { return res.ok && (res.headers.get('content-type') || '').indexOf('text/html') > -1; }

    // Ganti isi halaman di belakang dengan halaman tujuan (tanpa muat ulang penuh)
    function gantiHalaman(html, url) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var baru = doc.querySelector('main'), lama = document.querySelector('main');
        if (!baru || !lama) { window.location.href = url; return; }
        lama.classList.add('sedang-ganti');
        setTimeout(function () {
            lama.innerHTML = baru.innerHTML;
            var h1Baru = doc.querySelector('h1'), h1Lama = document.querySelector('h1');
            if (h1Baru && h1Lama) h1Lama.textContent = h1Baru.textContent;
            document.title = doc.title || document.title;
            if (url && url !== location.href) history.pushState({}, '', url);
            jalankanScript(lama); bungkusTabel(lama);
            lama.classList.remove('sedang-ganti'); window.scrollTo({ top: 0 });
            tutup();
        }, 150);
    }
    window.addEventListener('popstate', function () { location.reload(); });

    function buka(url) {
        urlAwal = url; urlSaatIni = url;
        kotak.classList.add('buka'); document.body.classList.add('popup-terbuka');
        judul.textContent = 'Memuat…'; isi.innerHTML = '<p class="popup-muat">Memuat…</p>';
        fetch(url, { headers: hdrModal, credentials: 'same-origin' }).then(function (res) {
            if (!htmlOk(res) || res.url.indexOf('/login') > -1) { window.location.href = url; return; }
            return res.text().then(function (t) { tampil(t, res.url); });
        }).catch(function () { window.location.href = url; });
    }
    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('a[data-popup]') : null;
        if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) return;
        e.preventDefault(); buka(a.href);
    });
    document.getElementById('popup-tutup').addEventListener('click', tutup);
    kotak.addEventListener('mousedown', function (e) { if (e.target === kotak) tutup(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && kotak.classList.contains('buka')) { var d = document.getElementById('edu-dialog'); if (!d || d.hidden) tutup(); }
    });

    // Kirim form dari dalam pop-up. Jika tujuan akhirnya halaman yang sedang/awal dibuka di pop-up (mis. setelah konfirmasi
    // password -> kembali ke akun yang dibuka, atau gagal validasi), isi pop-up diganti. Selain itu halaman di belakang diperbarui.
    function kirimDariPopup(f) {
        return fetch(f.action, { method: 'POST', body: new FormData(f), headers: hdrPopup(), credentials: 'same-origin', referrer: urlSaatIni || undefined }).then(function (res) {
            if (!htmlOk(res)) { f.submit(); return; }
            if (sama(res.url, urlSaatIni) || sama(res.url, urlAwal)) { return res.text().then(function (t) { tampil(t, res.url); }); }
            return fetch(res.url, { headers: hdrBiasa, credentials: 'same-origin' }).then(function (r2) {
                if (htmlOk(r2)) { return r2.text().then(function (t) { gantiHalaman(t, r2.url); }); }
                window.location.href = res.url;
            });
        }).catch(function () { f.submit(); });
    }
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!isi.contains(f) || (f.method || '').toLowerCase() !== 'post' || (f.dataset && f.dataset.konfirmasi)) return;
        e.preventDefault();
        var tombol = f.querySelector('button[type=submit],button:not([type])'); if (tombol) tombol.disabled = true;
        kirimDariPopup(f);
    });

    // Hapus/aksi berkonfirmasi (setelah dialog disetujui): di dalam pop-up memakai alur pop-up, di halaman biasa langsung memperbarui halaman.
    window.eduKirim = function (f) {
        if (isi.contains(f)) { kirimDariPopup(f); return; }
        fetch(f.action, { method: 'POST', body: new FormData(f), headers: hdrBiasa, credentials: 'same-origin' }).then(function (res) {
            if (htmlOk(res)) { return res.text().then(function (t) { gantiHalaman(t, res.url); }); }
            f.submit();
        }).catch(function () { f.submit(); });
    };
})();
</script>
