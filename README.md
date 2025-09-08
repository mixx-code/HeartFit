<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Setup Proyek (Laragon + Laravel 12)

Panduan singkat menjalankan proyek Laravel 12 yang di-clone dari GitHub menggunakan **Laragon** di Windows. Ikuti langkah **A s.d. D** berurutan.

---

## A. Konfigurasi `.env`

Salin file contoh lalu sesuaikan koneksi database (default Laragon: user `root`, tanpa password).

**PowerShell (Windows):**

    Copy-Item .env.example .env

**CMD:**

    copy .env.example .env

Edit `.env` (minimal seperti ini):

    APP_NAME="Laravel"
    APP_ENV=local
    APP_KEY=                # akan diisi otomatis di langkah B
    APP_DEBUG=true
    APP_URL=http://nama-project.test   # domain auto Laragon sesuai nama folder

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_laravel
    DB_USERNAME=root
    DB_PASSWORD=

**Catatan Laragon:** Aktifkan **Auto Virtual Hosts** agar domain `http://nama-project.test` otomatis mengarah ke folder `public/`. Jika tidak, gunakan `php artisan serve` (lihat Bagian D).

---

## B. Install Dependencies & Generate App Key

Jalankan di root project:

    composer install
    php artisan key:generate

Kenapa perlu?

- `composer install` mengunduh library ke folder `vendor` (di-ignore Git).
- `php artisan key:generate` mengisi `APP_KEY` untuk enkripsi session/token.

---

## C. Database Migration & Storage Link

Pastikan service MySQL Laragon berjalan dan database sudah dibuat (mis. `db_laravel`).

    php artisan migrate
    # jika ada seeder:
    # php artisan migrate --seed

Buat link storage ke public (agar file bisa diakses web server):

    php artisan storage:link

Kenapa perlu?

- Setelah clone, struktur tabel belum ada — `migrate` membuatnya.
- `storage:link` membuat symlink `public/storage` (folder ini di-ignore Git).

---

## D. Menjalankan Aplikasi

### Opsi 1 — Laragon (Auto Virtual Hosts)

1. Buka Laragon → **Start All**.
2. Akses: `http://nama-project.test`  
   *(nama domain mengikuti nama folder project di `laragon/www`)*

### Opsi 2 — Built-in PHP Server

    php artisan serve

Buka: `http://127.0.0.1:8000`

**(Jika memakai Vite untuk asset)**
Di terminal terpisah:

    npm install
    npm run dev
    # atau build produksi:
    # npm run build

---

## Troubleshooting Cepat

Jika konfigurasi berubah atau tampilan tidak ter-update:

    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

Selesai. 🚀

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
