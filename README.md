# Project Description

Aplikasi untuk mengelola data buku, peminjaman dam denda keterlambatan. Aplikasi dirancang sebagai onsite app dan admin oriented.

# Features
- [x] Authentication and Authorization (role-based)
- [x] Pengelolaan keanggotaan
- [x] Penyimpanan buku dengan data DCC, lokasi rak dan lainnya.
- [x] Peminjaman dan pengembalian (admin only)
- [x] Konfigurasi jumlah peminjamam, batas peminjaman (hari) dan denda peminjaman (per hari).
- [x] Laporan excel keanggotaan, buku, peminjaman, pengembalian dan denda (admin and manager)

# Tech Stack

Kebutuhan dasar :
- PHP >= 8.0
- MYSQL
- Composer and Nodejs

Tech Stack dari aplikasi :
- Laravel
- TailwindCSS 
- AlpineJS
- Laravel Excel

# Installation

```bash
git clone htttps://github.com/azharisikumbang/perpustakaan
cd perpustakaan

composer install 

# set database config in .env file
php artisan migrate --seed

# build the front end
npm install 
npm run dev

# serve the app
php artisan serve

```


