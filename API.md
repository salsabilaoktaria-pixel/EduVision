# API EduVision untuk Aplikasi Komite

Basis URL: `https://DOMAIN-ANDA/api/v1`
Autentikasi: kirim kunci di header `X-API-Key: evk_xxxxxxxx` (atau `Authorization: Bearer evk_xxxxxxxx`).
Kunci dibuat admin di menu **Integrasi API**. Batas 60 permintaan/menit. Semua jawaban berupa JSON.
Kunci kosong/salah/nonaktif → `401`. NISN tidak ditemukan → `404`. Validasi gagal → `422`.

| Method | Path | Fungsi |
|---|---|---|
| GET | `/kelas` | Daftar kelas + jumlah siswa |
| GET | `/siswa?kelas_id=&q=&per_halaman=50&page=1` | Daftar siswa (cari nama/NISN) |
| GET | `/siswa/{nisn}` | Satu siswa |
| GET | `/siswa/{nisn}/spp` | Tagihan SPP siswa (hanya baca) |
| GET | `/siswa/{nisn}/komite` | Status komite yang tersimpan di EduVision |
| POST | `/siswa/{nisn}/komite` | Kirim/perbarui status komite (idempoten per `periode`) |

Field siswa: `nisn, nama, jenis_kelamin, kelas, tingkat, nama_wali, no_hp_wali`.
Data sensitif (alamat, tanggal lahir, foto, nilai, absensi) tidak dibuka.

## Contoh
```bash
curl -H "X-API-Key: evk_xxx" "https://DOMAIN/api/v1/siswa?kelas_id=1"

curl -X POST -H "X-API-Key: evk_xxx" -H "Content-Type: application/json" \
  -d '{"periode":"2026/2027","nominal":500000,"status":"Lunas","referensi":"KMT-0001","dibayar_pada":"2026-10-20 09:00:00"}' \
  https://DOMAIN/api/v1/siswa/0012345678/komite
```
`status` ∈ `Lunas | Belum Lunas | Cicilan`. Siswa otomatis mendapat notifikasi saat status berubah.

## Saran integrasi
Gunakan **NISN sebagai kunci penghubung**. Komite tetap menjadi sumber kebenaran pembayaran komite;
EduVision hanya menyimpan cermin statusnya (tabel `komite_pembayaran`) agar tampil di detail siswa dan menotifikasi siswa.
Jangan menggabung dengan status SPP: nominal, bendahara, dan periodenya berbeda.
