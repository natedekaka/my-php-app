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

```bash
# Start container
podman-compose up -d

# Buka phpMyAdmin
# http://localhost:8091
# Login: root / rootpass

# Buat database: qooz_db
# Import file: src/qooz/mysql-schema.sql
```

### 3. Konfigurasi API

Edit `src/qooz/.env.local`:
```env
NEXT_PUBLIC_API_URL=http://localhost:8090/qooz/api
```

Untuk akses dari HP lain di jaringan sama:
```env
NEXT_PUBLIC_API_URL=http://192.168.x.x:8090/qooz/api
```

Ganti `192.168.x.x` dengan IP server kamu.

### 4. Jalankan Frontend Next.js

```bash
cd src/qooz
npm install
npm run dev
```

## Cara Pakai

### Sebagai Guru (Host)

1. Buka `http://localhost:3000/host`
2. Login dengan:
   - Email: `guru@test.com`
   - Password: `guru123`
3. Buat kuis baru
4. Tambah pertanyaan (soal, opsi A/B/C/D, jawaban benar)
5. Klik "Mulai Kuis"
6. Berikan Game PIN ke siswa
7. Klik "Akhiri Setiap Soal" untuk hitung skor
8. Klik "Soal Berikutnya" untuk lanjut

### Sebagai Siswa (Player)

1. Buka `http://192.168.x.x:3000/play` (dari HP/laptop)
2. Masukkan Game PIN dari guru
3. Masukkan nama
4. Klik opsi jawaban saat soal muncul
5. Lihat skor dan peringkat

## Kapasitas

| Pengguna | Status |
|----------|--------|
| 50-100 | ✅ Lancar |
| 200-300 | ✅ Perlu RAM cukup |
| 500+ | ⚠️ Butuh server lebih kuat |

## Troubleshooting

### Error "Forbidden" saat akses API
- Pastikan `src/qooz/.htaccess` ada

### Tidak bisa akses dari HP
- Buka firewall: `sudo ufw allow 3000/tcp`
- Pastikan IP di `.env.local` sesuai IP server

### Skor selalu 0
- Pastikan klik "Akhiri Setiap Soal" setelah waktu habis
- Cek database ada di `qooz_db`

### Port 3000 sudah dipakai
```bash
lsof -ti:3000 | xargs kill -9
```

## Struktur Folder

```
src/qooz/
├── api/              # Backend PHP API
│   ├── auth/        # Login/register
│   ├── game/        # Game logic
│   ├── player/      # Player actions
│   └── quiz/        # Quiz management
├── src/app/         # Frontend Next.js
│   ├── host/        # Halaman guru
│   └── play/        # Halaman siswa
├── mysql-schema.sql # Database schema
└── package.json     # Dependencies
```

## Credit

- Dibuat dengan Next.js 16 + React 19
- Backend: PHP 8.4 + MySQL
- UI: TailwindCSS
