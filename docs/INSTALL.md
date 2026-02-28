# Panduan Instalasi Aplikasi Absensi Siswa

## Persiapan

Pastikan komputer Anda memiliki:
- Web Server (XAMPP/WAMP/LAMPP) ATAU Docker
- Browser (Chrome/Firefox/Edge)
- Text Editor (VS Code/Notepad++/Sublime)

---

## METODE 1: Menggunakan Docker ( Paling Mudah )

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
git clone <repository-url> absensi-siswa
cd absensi-siswa
```

### Langkah 3: Jalankan Aplikasi
```bash
docker-compose up -d
```

### Langkah 4: Akses Aplikasi
- **Web App**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Server: `db`
  - Username: `root`
  - Password: `rootpass`

### Langkah 5: Login
- Username: `admin`
- Password: `admin123`

---

## METODE 2: Manual (XAMPP)

### Langkah 1: Install XAMPP
1. Download XAMPP dari https://apachefriends.org
2. Install dengan default settings
3. Start Apache dan MySQL

### Langkah 2: Clone/Copy Project
1. Copy folder `src/absensi-siswa` ke `C:\xampp\htdocs\` (Windows) atau `/opt/lampp/htdocs/` (Linux)
2. Rename menjadi `absensi-siswa`

### Langkah 3: Buat Database
1. Buka http://localhost/phpmyadmin
2. Klik "New" → buat database `absensi_siswa`
3. Klik database → Import → Choose File
4. Pilih file `database.sql` → Go

### Langkah 4: Konfigurasi Database
Edit file `absensi-siswa/config.php`:
```php
$host = 'localhost'; 
$user = 'root'; 
$pass = '';  // Kosongkan jika pake XAMPP default
$db   = 'absensi_siswa';
```

### Langkah 5: Akses Aplikasi
Buka http://localhost/absensi-siswa

---

## METODE 3: Menggunakan Laravel Valet (Mac/Linux)

### Langkah 1: Install Requirements
```bash
# Mac
brew install php mysql composer

# Linux
sudo apt install php mysql-server composer
```

### Langkah 2: Setup
```bash
cd absensi-siswa/src
composer install
cp .env.example .env
# Edit .env dengan konfigurasi database
php artisan key:generate
```

---

## Konfigurasi Awal Setelah Install

### 1. Login
```
Username: admin
Password: admin123
```

### 2. Buat Tahun Ajaran Baru
1. Klik menu **Tahun Ajaran**
2. Klik **Tambah**, isi:
   - Nama: 2026/2027
3. Klik **Tambah Semester**:
   - Semester: 1
   - Tanggal Mulai: 2026-07-14
   - Tanggal Selesai: 2026-12-20
4. Klik **Aktifkan** pada semester yang berjalan

### 3. Import Kelas (Optional)
1. Buat file CSV:
   ```csv
   nama_kelas,wali_kelas
   X IPA 1,Budi Santoso
   X IPA 2,Siti Rahayu
   ```
2. Buka menu **Kelas** → **Import**
3. Upload file CSV

### 4. Import Siswa (Optional)
1. Buka phpMyAdmin, cek ID kelas yang baru dibuat
2. Buat file CSV:
   ```csv
   nis,nisn,nama,kelas_id,jenis_kelamin
   2026001,1234567890,Andi Wijaya,1,Laki-laki
   ```
3. Buka menu **Siswa** → **Import**
4. Upload file CSV

---

## Troubleshooting

### Error: "Koneksi Gagal"
- Cek konfigurasi di `config.php`
- Pastikan MySQL sedang berjalan
- Cek username & password MySQL

### Error: "Table doesn't exist"
- Import ulang file `database.sql` melalui phpMyAdmin

### Error: "Session not started"
- Cek folder `sessions` ada dan writable
- Di XAMPP: `C:\xampp\tmp`

### Aplikasi Lambat
- Gunakan PHP 8.0+ untuk performa optimal
- Pastikan cukup memory limit

---

## Support

Jika ada pertanyaan, hubungi: [Email Anda]

---

**Catatan**: Ganti password default setelah login pertama kali untuk keamanan!
