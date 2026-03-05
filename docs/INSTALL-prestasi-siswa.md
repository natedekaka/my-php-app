# Panduan Instalasi Aplikasi Prestasi Siswa

## Persiapan

Pastikan komputer Anda memiliki:
- Web Server (XAMPP/WAMP/LAMPP) ATAU Docker
- Browser (Chrome/Firefox/Edge)
- Text Editor (VS Code/Notepad++/Sublime)

---

## METODE 1: Menggunakan Docker (Paling Mudah)

### Langkah 1: Install Docker
- **Windows**: Download Docker Desktop dari https://docker.com
- **Linux**: 
  ```bash
  sudo apt update
  sudo apt install docker.io docker-compose
  sudo systemctl start docker
  ```
- **Mac**: Download Docker Desktop dari https://docker.com

### Langkah 2: Clone Project
```bash
git clone <repository-url> prestasi-siswa
cd prestasi-siswa
```

### Langkah 3: Konfigurasi Docker
Pastikan file `docker-compose.yml` sudah dikonfigurasi untuk prestasi-siswa. Edit jika perlu:
```yaml
services:
  app:
    ports:
      - "8082:80"  # Port untuk aplikasi
  db:
    # Konfigurasi database MySQL
  phpmyadmin:
    ports:
      - "8083:80"  # Port untuk phpMyAdmin
```

### Langkah 4: Jalankan Aplikasi
```bash
docker-compose up -d
```

### Langkah 5: Setup Database
1. Buka phpMyAdmin: http://localhost:8083
2. Buat database baru: `prestasi_siswa_db`
3. Import file `src/prestasi-siswa/prestasi_siswa_db.sql`

### Langkah 6: Akses Aplikasi
- **Web App**: http://localhost:8082/prestasi-siswa
- **Admin Panel**: http://localhost:8082/prestasi-siswa/admin

### Langkah 7: Login Admin
- Username: `admin`
- Password: `admin123`

---

## METODE 2: Manual (XAMPP)

### Langkah 1: Install XAMPP
1. Download XAMPP dari https://apachefriends.org
2. Install dengan default settings
3. Start Apache dan MySQL

### Langkah 2: Copy Project
1. Copy folder `src/prestasi-siswa` ke `C:\xampp\htdocs\` (Windows) atau `/opt/lampp/htdocs/` (Linux)
2. Rename menjadi `prestasi-siswa`

### Langkah 3: Buat Database
1. Buka http://localhost/phpmyadmin
2. Klik "New" → buat database `prestasi_siswa_db`
3. Klik database → Import → Choose File
4. Pilih file `prestasi_siswa_db.sql` → Go

### Langkah 4: Konfigurasi Database
Edit file `prestasi-siswa/config.php`:
```php
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASS', '');  // Kosongkan jika pake XAMPP default
define('DB_NAME', 'prestasi_siswa_db');
```

### Langkah 5: Akses Aplikasi
- **Web App**: http://localhost/prestasi-siswa
- **Admin Panel**: http://localhost/prestasi-siswa/admin

---

## METODE 3: Menggunakan PHP Native + MySQL

### Langkah 1: Install Requirements
```bash
# Linux (Ubuntu/Debian)
sudo apt install php mysql-server apache2

# Atau menggunakan XAMPP
```

### Langkah 2: Setup Database
```bash
mysql -u root -p
CREATE DATABASE prestasi_siswa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Langkah 3: Import Database
```bash
mysql -u root -p prestasi_siswa_db < src/prestasi-siswa/prestasi_siswa_db.sql
```

### Langkah 4: Konfigurasi
Edit `src/prestasi-siswa/config.php` sesuai konfigurasi MySQL Anda.

### Langkah 5: Jalankan
```bash
# Copy ke folder web server
sudo cp -r src/prestasi-siswa /var/www/html/

# Atur permission
sudo chmod -R 755 /var/www/html/prestasi-siswa
sudo chmod -R 777 /var/www/html/prestasi-siswa/uploads
```

---

## Konfigurasi Awal Setelah Install

### 1. Login Admin
```
Username: admin
Password: admin123
```

### 2. Tambah Prestasi
1. Klik menu **Tambah Prestasi**
2. Isi form:
   - Jenis Prestasi: Akademik/Non-Akademik
   - Nama Prestasi: (isi nama kompetisi)
   - Tingkat: Sekolah/Kabupaten/Provinsi/Nasional/Internasional
   - Tanggal: Tanggal penerimaan prestasi
   - Siswa: Pilih siswa (untuk prestasi individu)
   - Kelompok: (untuk prestasi tim/kelompok)
   - Guru Pembimbing: Pilih guru
   - Foto: Upload bukti prestasi
3. Klik Simpan

### 3. Tambah Prestasi Guru
1. Klik menu **Prestasi Guru**
2. Isi form prestasi guru
3. Upload foto bukti

### 4. Tambah Prestasi Sekolah
1. Klik menu **Prestasi Sekolah**
2. Isi form sejarah/prestasi sekolah
3. Upload foto

### 5. Alumni PTN
1. Klik menu **Alumni PTN**
2. Tambah data alumni yang diterima di PTN
3. Include foto alumni

---

## Fitur Aplikasi

### Public Page (index.php)
- Tampilan semua prestasi siswa
- Filter berdasarkan tahun/tipe
- Tampilan prestasi guru
- Tampilan sejarah/prestasi sekolah
- Data alumni PTN

### Admin Panel (admin/)
- Dashboard dengan statistik
- Kelola Prestasi Siswa
- Kelola Prestasi Guru
- Kelola Prestasi Sekolah
- Kelola Alumni PTN
- Export laporan ke Excel
- Filter berdasarkan tahun ajaran

---

## Troubleshooting

### Error: "Koneksi Gagal"
- Cek konfigurasi di `config.php`
- Pastikan MySQL sedang berjalan
- Cek username & password MySQL

### Error: "Table doesn't exist"
- Import ulang file `prestasi_siswa_db.sql` melalui phpMyAdmin

### Error: "Upload failed"
- Pastikan folder `uploads/` ada dan writable
- Cek max_upload_size di php.ini

### Error: "Session not started"
- Cek konfigurasi session di PHP

### Aplikasi Lambat
- Gunakan PHP 8.0+ untuk performa optimal
- Optimize gambar sebelum upload

---

## Struktur Direktori

```
prestasi-siswa/
├── config.php              # Konfigurasi database
├── index.php               # Halaman publik
├── prestasi_siswa_db.sql   # Database aplikasi
├── admin/
│   ├── index.php           # Dashboard admin
│   ├── login.php           # Halaman login admin
│   └── logout.php          # Logout
├── api/
│   ├── prestasi.php        # API prestasi siswa
│   ├── prestasi_guru.php   # API prestasi guru
│   ├── prestasi_sekolah.php# API prestasi sekolah
│   ├── alumni_ptn.php      # API alumni PTN
│   ├── rekapan.php         # API rekapitulasi
│   ├── export.php          # Export Excel
│   └── stats.php           # Statistik
└── uploads/                # Folder upload foto
```

---

## Akun Default

| Username | Password |
|----------|----------|
| admin | admin123 |

---

**Catatan**: Ganti password default setelah login pertama kali untuk keamanan!

---

## Support

Jika ada pertanyaan, hubungi: [Email Anda]
