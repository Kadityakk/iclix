# iClix

Website streaming film dan serial bergaya Netflix, dibangun dengan Laravel 13, Blade, Alpine.js, dan Tailwind CSS 4. Spesifikasi lengkapnya ada di [PRD.md](PRD.md).

## Menjalankan di komputer baru

Butuh PHP 8.3+ (dengan ekstensi `pdo_sqlite`, `fileinfo`, `gd`), Composer, dan Node.js.

```sh
composer run setup
php artisan serve
```

Buka <http://localhost:8000>, lalu masuk dengan salah satu akun demo. Password keduanya `password`.

| Peran | Email |
| --- | --- |
| Admin | `admin@iclix.test` |
| Member (sudah berlangganan) | `member@iclix.test` |

Ganti atau hapus kedua akun ini sebelum situs dibuka ke publik.

## Perintah harian

```sh
composer run dev                  # server dan proses pengembangan lainnya
php artisan test --compact        # semua tes
vendor/bin/pint --dirty           # rapikan kode PHP
php artisan migrate:fresh --seed  # kembalikan ke data demo
```

## Catatan

- **Pembayaran** masih simulasi. Tombol Bayar langsung mengaktifkan langganan tanpa memungut biaya.
- **Database** bawaan adalah SQLite. Untuk MySQL, isi `DB_*` di `.env`, lalu jalankan `php artisan migrate --seed`.
- **Upload video** sampai 500 MB membutuhkan `upload_max_filesize` dan `post_max_size` minimal `512M` di `php.ini`.
- **Email reset password** ditulis ke `storage/logs/laravel.log` sampai `MAIL_*` di `.env` diatur.
- **Konten demo** berisi film dan serial asli. Judul, sinopsis, poster, rating, dan detail lain diambil dari IMDb lewat OMDb API. Isi `OMDB_API_KEY` di `.env` (kunci gratis di omdbapi.com), lalu jalankan `php artisan imdb:sync`. Videonya tetap klip contoh 45 detik.

## Efek parallax

Lapisan bergerak dengan kecepatan berbeda saat halaman digulir dan mengikuti kursor di perangkat yang mendukung hover. Direktif `x-parallax` tersedia, tetapi tampilan saat ini sengaja dibuat polos tanpa efek ini. Efeknya mati otomatis jika perangkat diatur ke `prefers-reduced-motion`. Kodenya ada di `resources/js/parallax.js`.
