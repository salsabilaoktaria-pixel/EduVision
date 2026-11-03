@echo off
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo Selesai. Silakan jalankan: php artisan serve
