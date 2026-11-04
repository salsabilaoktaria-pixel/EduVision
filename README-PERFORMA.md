# Panduan Performa EduVision

## Kenapa sekarang jauh lebih cepat
- CSS Tailwind, Alpine.js, Chart.js, QR Code, dan font DM Sans sekarang **disimpan lokal** (`public/css/app.css`, `public/vendor/`, `public/fonts/`). Dulu semuanya diunduh dari internet (CDN) di setiap halaman dan Tailwind dikompilasi di browser, itu penyebab loading lama saat pindah halaman.
- Chart.js hanya dimuat di halaman Dashboard.
- Daftar semester disimpan di cache 2 menit (dulu ±14 query database di setiap halaman).
- Aplikasi tetap tampil rapi saat internet mati.

## Biar makin ngebut di XAMPP (Windows)
1. Edit `C:\xampp\php\php.ini`, cari dan aktifkan (hapus tanda `;`):
   ```
   zend_extension=opcache
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   ```
   Restart Apache / jalankan ulang `php artisan serve`. Ini biasanya mempercepat 2-3 kali lipat.
2. Di `.env` ubah `APP_DEBUG=false` dan `LOG_LEVEL=warning` (khusus saat presentasi/produksi).
3. Jalankan `php artisan optimize` (menyimpan cache config, rute, dan view). Setelah mengubah kode, jalankan `php artisan optimize:clear`.

## Build ulang CSS (hanya jika menambah class Tailwind baru)
File `public/css/app.css` sudah jadi, jadi **tidak perlu** menjalankan ini kecuali Anda menulis class Tailwind baru yang belum pernah dipakai.
```
npm install -D tailwindcss@3.4.19
npx tailwindcss -c tailwind.config.cjs -i resources/css/app.css -o public/css/app.css --minify
```
Kalau `public/css/app.css` dihapus, web otomatis memakai mode CDN lama (lambat, butuh internet).
