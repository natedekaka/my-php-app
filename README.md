# Aplikasi Absensi Siswa

Sistem absensi siswa berbasis PHP dan MySQL dengan fitur manajemen tahun ajaran dan semester.

## Fitur

- ✅ Login Multiple User (Admin, Guru)
- ✅ CRUD Data Siswa (NIS, Nama, Kelas, Foto)
- ✅ CRUD Data Kelas
- ✅ CRUD Tahun Ajaran & Semester
- ✅ Absensi Siswa (Hadir, Sakit, Izin, Alfa, Terlambat) per Tanggal & Semester
- ✅ Rekap Absensi per Kelas per Semester
- ✅ Rekap Absensi per Siswa
- ✅ Rekap Absensi per Tanggal
- ✅ Export/Cetak Laporan (PDF/Print)
- ✅ Dashboard Statistik Kehadiran
- ✅ Import Data Siswa & Kelas via CSV

## Requirements

- PHP 8.0+
- MySQL 8.0+
- Web Server (Apache/Nginx)
- Browser Modern (Chrome, Firefox, Edge)

## Struktur Direktori

```
my-php-app/
├── docker-compose.yml     # Konfigurasi Docker
├── Dockerfile             # Konfigurasi image PHP
├── database.sql           # Database lengkap (untuk instalasi awal)
├── src/
│   └── absensi-siswa/
│       ├── config.php          # Konfigurasi database
│       ├── login.php           # Halaman login
│       ├── proses_login.php    # Proses autentikasi
│       ├── logout.php          # Logout
│       ├── dashboard/          # Halaman utama setelah login
│       ├── siswa/              # Manajemen data siswa (CRUD + import + export)
│       ├── kelas/              # Manajemen data kelas (CRUD + import)
│       ├── absen/              # Input absensi harian
│       ├── rekap/              # Laporan dan rekap absensi
│       ├── tahun_ajaran/       # Manajemen tahun ajaran & semester
│       └── includes/           # Header, footer, fungsi helper
```

## Cara Install

### Cara 1: Menggunakan Docker (Recommended)

1. Clone repository ini:
   ```bash
   git clone https://github.com/natedekaka/my-php-app.git
   cd my-php-app
   ```

2. Clone aplikasi absensi-siswa:
   ```bash
   git clone https://github.com/natedekaka/absensi-siswa.git src/absensi-siswa
   ```

3. Jalankan container:
   ```bash
   docker-compose up -d
   ```

4. Tunggu beberapa saat hingga semua container berjalan

5. Buka browser:
   - **Aplikasi**: http://localhost:8082/absensi-siswa
   - **phpMyAdmin**: http://localhost:8083
     - Server: db
     - Username: user
     - Password: pass123
     - Database: myapp

6. Login dengan akun default:
   | Username | Password |
   |----------|----------|
   | admin | (password yang dibuat saat install)

### Cara 2: Manual (XAMPP/LAMPP)

1. Clone repository ini ke folder web server (htdocs/www):
   ```bash
   git clone https://github.com/natedekaka/my-php-app.git
   ```

2. Clone aplikasi absensi-siswa:
   ```bash
   cd my-php-app
   git clone https://github.com/natedekaka/absensi-siswa.git src/absensi-siswa
   ```

3. Buat database baru (nama bebas, contoh: `absensi_siswa`):
   ```sql
   CREATE DATABASE absensi_siswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Import database:
   ```bash
   mysql -u root -p absensi_siswa < database.sql
   ```

5. Edit konfigurasi database di `src/absensi-siswa/config.php`:
   ```php
   $host = 'localhost'; 
   $user = 'root'; 
   $pass = '';  // Sesuaikan dengan password MySQL Anda
   $db   = 'absensi_siswa';
   ```

6. Buka browser: http://localhost/my-php-app/src/absensi-siswa

## Akun Default

Setelah instalasi, login dengan:

| Username | Password |
|----------|----------|
| admin | (password yang Anda buat) |

**Catatan**: Password default di-hash dengan bcrypt. Saat pertama kali install, gunakan password yang sudah di-set di database atau buat user baru melalui phpMyAdmin.

## Cara Penggunaan

### 1. Setup Tahun Ajaran
1. Login sebagai Admin
2. Buka menu **Tahun Ajaran**
3. Tambah tahun ajaran (contoh: 2026/2027)
4. Tambah semester 1 dan 2 dengan tanggal mulai/selesai
5. Aktifkan semester yang sedang berjalan

### 2. Import Data
1. **Import Kelas**: Menu Kelas → Import
2. **Import Siswa**: Menu Siswa → Import
   - Format: nis,nisn,nama,kelas_id,jenis_kelamin

### 3. Input Absensi
1. Buka menu **Absensi**
2. Pilih semester dan kelas
3. Pilih tanggal
4. Pilih status kehadiran masing-masing siswa
5. Klik Simpan

### 4. Lihat Rekap
- **Per Kelas**: Rekap → Per Kelas
- **Per Siswa**: Rekap → Per Siswa  
- **Per Tanggal**: Rekap → Per Tanggal

## Teknologi

- **Frontend**: Bootstrap 5, Font Awesome, Chart.js
- **Backend**: PHP 8 Native
- **Database**: MySQL 8
- **CSS**: Custom WhatsApp-style theme

## Troubleshooting

### Docker tidak bisa diakses setelah restart
Jalankan perintah berikut:
```bash
cd my-php-app
docker-compose down
docker-compose up -d
```

### Port sudah digunakan
Jika port 8082 atau 8083 sudah digunakan, edit `docker-compose.yml`:
```yaml
services:
  app:
    ports:
      - "9092:80"  # Ganti port lain
  phpmyadmin:
    ports:
      - "9093:80"  # Ganti port lain
```

### Cara buat password baru untuk admin
Buka phpMyAdmin → database `myapp` → tabel `users` → insert user baru dengan password yang di-hash:

```php
// Generate hash password di PHP:
echo password_hash('password_baru', PASSWORD_DEFAULT);
```

Lalu masukkan hash tersebut ke kolom `password` di tabel `users`.

## Lisensi

MIT License - Bebas digunakan dan dimodifikasi

## Author

Dibuat untuk memudahkan pengelolaan absensi siswa di sekolah.
