{{-- Dialog konfirmasi & pesan bergaya EduVision: pengganti kotak OK/Cancel bawaan browser.
     Dipakai otomatis oleh setiap <form data-konfirmasi="..."> dan oleh alert(). --}}
<div id="edu-dialog" class="edu-dialog" hidden role="dialog" aria-modal="true" aria-labelledby="edu-dialog-judul">
    <div class="edu-dialog-kotak">
        <div class="edu-dialog-ikon" id="edu-dialog-ikon"></div>
        <h3 id="edu-dialog-judul" class="edu-dialog-judul">Konfirmasi</h3>
        <p id="edu-dialog-isi" class="edu-dialog-isi"></p>
        <div class="edu-dialog-aksi">
            <button type="button" id="edu-dialog-batal" class="btn-aksi btn-detail">Batal</button>
            <button type="button" id="edu-dialog-ok" class="btn-aksi btn-edit">Ya, lanjutkan</button>
        </div>
    </div>
</div>
<script>
(function () {
    const dlg = document.getElementById('edu-dialog'); if (!dlg) return;
    const $ = id => document.getElementById(id);
    const ikonBahaya = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="26" height="26"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>';
    const ikonTanya = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="26" height="26"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>';
    let aksiOk = null;
    function tutup() { dlg.hidden = true; document.body.classList.remove('edu-dialog-buka'); aksiOk = null; }
    function buka(teks, opsi) {
        const bahaya = opsi.bahaya;
        dlg.classList.toggle('edu-bahaya', bahaya);
        $('edu-dialog-judul').textContent = opsi.judul;
        $('edu-dialog-isi').textContent = teks;
        $('edu-dialog-ikon').innerHTML = bahaya ? ikonBahaya : ikonTanya;
        $('edu-dialog-ok').textContent = opsi.ok;
        $('edu-dialog-ok').className = 'btn-aksi ' + (bahaya ? 'btn-hapus' : 'btn-detail');
        $('edu-dialog-batal').style.display = opsi.info ? 'none' : '';
        aksiOk = opsi.aksi || null;
        dlg.hidden = false; document.body.classList.add('edu-dialog-buka');
        $('edu-dialog-ok').focus();
    }
    $('edu-dialog-ok').addEventListener('click', () => { const a = aksiOk; tutup(); if (a) a(); });
    $('edu-dialog-batal').addEventListener('click', tutup);
    dlg.addEventListener('click', e => { if (e.target === dlg) tutup(); });
    document.addEventListener('keydown', e => { if (!dlg.hidden && e.key === 'Escape') tutup(); });
    document.addEventListener('submit', function (e) {
        const f = e.target, teks = f.dataset ? f.dataset.konfirmasi : null;
        if (!teks || f.dataset.sudahOk) return;
        e.preventDefault();
        const bahaya = /^(hapus|tolak|buang)/i.test(teks);
        buka(teks, { bahaya, judul: bahaya ? 'Yakin ingin melanjutkan?' : 'Konfirmasi', ok: bahaya ? 'Ya, hapus' : 'Ya, lanjutkan',
            aksi: () => { f.dataset.sudahOk = '1'; f.submit(); } });
    }, true);
    window.alert = function (pesan) { buka(String(pesan), { bahaya: false, info: true, judul: 'Pemberitahuan', ok: 'Mengerti' }); };
})();
</script>
