# Aplikasi Prestasi Siswa

Sistem manajemen data prestasi siswa, guru, sekolah, dan alumni.

## Fitur

- Prestasi Siswa (perorangan & kelompok)
- Prestasi Guru
- Prestasi Sekolah
- Data Alumni (PTN/PTS/Bekerja)
- Export ke Excel
- Dashboard admin

## Installation

### Menggunakan Docker (Development)

```bash
# Clone/download aplikasi
# Buat folder src di root jika belum ada

# Start container
docker-compose up -d

# Akses aplikasi
http://localhost:8084
```

### Manual Install (Hosting/cPanel)

#### 1. Persiapan Database

1. Buat database MySQL baru (misalnya: `prestasi_siswa_db`)
2. Import file database:
   - `src/prestasi-siswa/prestasi_siswa_db.sql` - tabel utama
   - `src/prestasi-siswa/add_missing_tables.sql` - tabel tambahan (guru, prestasi_guru, prestasi_sekolah, alumni_ptn)

#### 2. Konfigurasi

Edit file `src/prestasi-siswa/config.php`:

```php
define('DB_HOST', 'localhost');        // atau host database Anda
define('DB_USER', 'username_db');     // username MySQL
define('DB_PASS', 'password_db');     // password MySQL
define('DB_NAME', 'prestasi_siswa_db'); // nama database
```

#### 3. Upload ke Hosting

1. Upload semua file di folder `src/prestasi-siswa/` ke public_html atau folder domain Anda
2. Pastikan folder `uploads/` memiliki permission 755 atau 775
3. Pastikan file config.php sudah dikonfigurasi dengan benar

#### 4. Akses

- Frontend: `https://domain-anda.com/`
- Backend: `https://domain-anda.com/admin/`
- Login default: `admin` / `admin`

## Default Login

```
Username: admin
Password: admin
```

## Struktur Folder

```
src/prestasi-siswa/
├── admin/           # Halaman admin
├── api/             # API endpoints
├── uploads/         # Upload foto/sertifikat
├── config.php       # Konfigurasi database
└── index.php        # Halaman utama (frontend)
```

## Tech Stack

- PHP 8.x
- MySQL 8.0
- Tailwind CSS
- Font Awesome
