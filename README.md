# RecycleHub

Sistem Informasi dan Transaksi Bank Sampah berbasis Laravel 13, MySQL/MariaDB, Bootstrap 5, dan JavaScript.

## Menjalankan di Laragon

1. Aktifkan Apache dan MySQL pada Laragon.
2. Buat database `recyclehub` melalui phpMyAdmin atau import `recyclehub.sql`.
3. Pastikan `.env` menggunakan `DB_DATABASE=recyclehub`, `DB_USERNAME=root`, dan `DB_PASSWORD=` sesuai konfigurasi Laragon.
4. Jalankan migration dan seeder: `php artisan migrate --seed`.
5. Jalankan `php artisan storage:link`.
6. Jalankan `php artisan serve`, kemudian buka `http://127.0.0.1:8000`.

Jika database sudah di-import melalui phpMyAdmin, jangan menjalankan migration. Gunakan migration hanya pada database kosong untuk membuat struktur yang konsisten.

## Akun awal

- Admin: `admin` / `Admin12345`
- Nasabah: `sitirahma` / `Nasabah12345`
