# Qooz - Kuis Interaktif Real-time

Qooz adalah aplikasi kuis interaktif mirip Kahoot untuk pembelajaran di kelas. Guru membuat kuis dan siswa menjawab melalui HP/laptop secara real-time.

## Fitur

- ✅ Buat kuis dengan pertanyaan pilihan ganda
- ✅ Game PIN untuk siswa join
- ✅ Skor berdasarkan kecepatan jawaban
- ✅ Leaderboard real-time
- ✅ Kapasitas tinggi (100-500 siswa)
- ✅ Multiplayer simultan

## Requirements

- Podman atau Docker
- Podman Compose
- Port 8090 (web), 8091 (phpmyadmin), 3000 (next.js)

## Cara Install

### 1. Clone Repo

```bash
git clone https://github.com/natedekaka/my-php-app.git
cd my-php-app
```

### 2. Setup Database

#### a. Start Container

```bash
podman-compose up -d
```

Tunggu beberapa detik hingga container running. Cek dengan:
```bash
podman ps
```

#### b. Buka phpMyAdmin

Buka browser dan akses: **http://localhost:8091**

Login dengan:
- **Username**: `root`
- **Password**: `rootpass`

#### c. Buat Database

1. Di sidebar kiri, klik "New Database"
2. Nama database: `qooz_db`
3. Collation: `utf8mb4_unicode_ci`
4. Klik "Create"

![Buat Database](https://via.placeholder.com/600x400?text=Buat+Database+qooz_db)

#### d. Import Schema

1. Klik database `qooz_db` di sidebar kiri
2. Pilih tab **"Import"**
3. Klik **"Choose File"**
4. Pilih file: `src/qooz/mysql-schema.sql`
5. Scroll ke bawah, klik **"Go"**

![Import Schema](https://via.placeholder.com/600x400?text=Import+mysql-schema.sql)

#### e. Verifikasi Tabel

Setelah import berhasil, cek apakah ada 6 tabel:
- `users` - data guru/admin
- `quizzes` - data kuis
- `questions` - data pertanyaan
- `game_sessions` - sesi game aktif
- `players` - data siswa yang join
- `answers` - jawaban siswa

Cek dengan klik tab "Structure" di `qooz_db`.

#### f. Tambah User Test (Opsional)

Untuk testing, sudah ada user default:
- Email: `guru@test.com`
- Password: `guru123`

Untuk tambah user manual via phpMyAdmin:
1. Klik tabel `users`
2. Pilih tab **"Insert"**
3. Isi:
   - `id`: (biarkan kosong/auto)
   - `email`: `guru@test.com`
   - `password_hash`: `$2y$12$p1l9XeoC5ed3z33evsRpPuTXjzcyVq9.sEpFXQeGsIDBHDhgDUASS` (untuk password: `guru123`)
   - `nama_lengkap`: `Guru Test`
4. Klik **"Go"**

### 3. Konfigurasi API

Edit file `src/qooz/.env.local`:

```bash
nano src/qooz/.env.local
```

Isi dengan:

```env
NEXT_PUBLIC_API_URL=http://localhost:8090/qooz/api
```

**Untuk akses dari HP lain di jaringan sama:**

Cek IP server dulu:
```bash
ip addr show | grep "192.168"
```

Contoh IP: `192.168.18.126`

Edit `.env.local`:
```env
NEXT_PUBLIC_API_URL=http://192.168.18.126:8090/qooz/api
```

### 4. Buka Port Firewall (Jika Perlu)

Jika akses dari HP tidak berhasil, buka port:

```bash
sudo ufw allow 3000/tcp
sudo ufw allow 8090/tcp
```

### 5. Jalankan Frontend Next.js

```bash
cd src/qooz
npm install
npm run dev
```

Akses aplikasi:
- **Lokal**: http://localhost:3000
- **Jaringan**: http://192.168.18.126:3000

---

## Cara Pakai

### Sebagai Guru (Host)

1. Buka `http://localhost:3000/host`
2. Login dengan:
   - Email: `guru@test.com`
   - Password: `guru123`
3. Klik **"+ Buat Kuis Baru"**
4. Isi judul dan deskripsi kuis
5. Klik **"+ Tambah Soal"**
6. Isi:
   - Pertanyaan
   - Opsi A, B, C, D
   - Jawaban benar (1=A, 2=B, 3=C, 4=D)
   - Waktu per soal (detik)
7. Klik **"Simpan"**
8. Ulangi langkah 5-7 untuk tambah soal lain
9. Klik **"Mulai Kuis"** di dashboard
10. Berikan **Game PIN** ke siswa
11. Klik **"Akhiri Setiap Soal"** setelah waktu habis untuk hitung skor
12. Klik **"Soal Berikutnya"** untuk lanjut ke soal berikutnya
13. Selesai semua soal, klik **"Selesai"** untuk lihat podium

### Sebagai Siswa (Player)

1. Buka `http://192.168.x.x:3000/play` (dari HP/laptop)
2. Masukkan **Game PIN** dari guru
3. Masukkan **nama** kamu
4. Klik **"Gabung"**
5. Tunggu guru memulai kuis
6. Saat soal muncul, klik salah satu opsi (A/B/C/D)
7. Lihat hasil: "BENAR!" atau "SALAH!"
8. Tunggu soal berikutnya
9. Selesai, lihat skor dan peringkat akhir

---

## Struktur Folder

```
src/qooz/
├── api/                    # Backend PHP API
│   ├── auth/               # Login/register
│   ├── game/               # Game logic (start, end_question, next)
│   ├── player/             # Player actions (join, answer, score)
│   └── quiz/               # Quiz management
├── src/app/                # Frontend Next.js
│   ├── host/               # Halaman guru
│   │   └── [quizId]/game/  # Game host page
│   └── play/               # Halaman siswa
│       └── [pin]/          # Player game page
├── mysql-schema.sql         # Database schema (6 tabel)
├── qooz_db/qooz_db.sql      # Sample data
├── package.json             # Dependencies Next.js
└── .env.local               # Konfigurasi API URL
```

---

## Kapasitas

| Pengguna | Status |
|----------|--------|
| 50-100 | ✅ Lancar |
| 200-300 | ✅ Perlu RAM cukup (2GB+) |
| 500+ | ⚠️ Butuh server lebih kuat (4GB+ RAM) |

### Optimasi Kapasitas

Aplikasi sudah dioptimasi dengan:
- Polling interval 2 detik (bukan 1 detik)
- Database indexes untuk query cepat
- Query optimization dengan JOIN
- In-memory caching
- PHP-FPM dynamic process

---

## Troubleshooting

### Error "Forbidden" saat akses API
```bash
# Pastikan .htaccess ada
ls -la src/qooz/.htaccess
```

Jika tidak ada, buat file:
```bash
echo 'Options -Indexes +FollowSymLinks
RewriteEngine On
RewriteBase /qooz/
Require all granted' > src/qooz/.htaccess
```

### Tidak bisa akses dari HP
1. Cek IP server:
```bash
ip addr show | grep "192.168"
```

2. Pastikan IP di `.env.local` sesuai IP server:
```bash
nano src/qooz/.env.local
```

3. Buka firewall:
```bash
sudo ufw allow 3000/tcp
sudo ufw allow 8090/tcp
sudo ufw reload
```

### Skor selalu 0
1. Pastikan klik **"Akhiri Setiap Soal"** setelah waktu habis
2. Cek database `qooz_db` sudah diimport dengan benar
3. Cek di phpMyAdmin - tabel `answers` harus ada data

### Port 3000 sudah dipakai
```bash
lsof -ti:3000 | xargs kill -9
# Atau
pkill -f "next dev"
```

### Container tidak start
```bash
podman-compose down
podman-compose up -d
podman logs my-php-app_app_1
```

---

## Credit

- Frontend: Next.js 16 + React 19 + TailwindCSS
- Backend: PHP 8.4 + MySQL
- Database: MariaDB/MySQL
- Container: Podman/Docker
