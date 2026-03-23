# E-Card Lebaran 1447 H

Aplikasi web berbasis PHP Native dan MariaDB/MySQL untuk membuat dan berbagi Kartu Hari Raya Idulfitri.

## Struktur Direktori

```
my-php-app/
├── src/
│   └── kartu-lebaran/
│       ├── assets/
│       │   ├── css/
│       │   ├── fonts/
│       │   ├── templates/
│       │   └── generated/
│       ├── includes/
│       │   ├── db.php
│       │   └── functions.php
│       ├── init.sql
│       ├── index.php
│       ├── proses.php
│       └── lihat.php
├── podman-compose.yml
└── README.md
```

## Port yang Digunakan

| Service | Port |
|---------|------|
| E-Card Web | 8092 |
| E-Card DB (MariaDB) | 3311 |
| PhpMyAdmin E-Card | 8093 |

## Instalasi

### 1. Set Izin Folder

```bash
chmod -R 777 /home/daniarsyah/my-php-app/src/kartu-lebaran/assets/generated
```

### 2. Download Font

```bash
cd /home/daniarsyah/my-php-app/src/kartu-lebaran/assets/fonts

# Download font Roboto
curl -L -o Roboto.ttf "https://github.com/googlefonts/roboto/raw/main/src/hinted/Roboto-Regular.ttf"
```

### 3. Generate Template Sample

```bash
cd /home/daniarsyah/my-php-app/src/kartu-lebaran
php setup-template.php
```

### 4. Jalankan Podman

```bash
cd /home/daniarsyah/my-php-app

# Jalankan container
podman-compose up -d

# Atau dengan docker-compose
docker-compose -f podman-compose.yml up -d
```

## Akses Aplikasi

| URL | Keterangan |
|-----|------------|
| http://localhost:8092 | E-Card Web |
| http://localhost:8093 | PhpMyAdmin (user: root, pass: ecard_root_pass) |

## Database

- **Host**: ecard-lebaran-db (container internal)
- **Database**: kartu_lebaran_db
- **User**: ecard_user
- **Password**: ecard_pass_2024

## Fitur Keamanan

- PDO Prepared Statements (SQL Injection prevention)
- CSRF Protection dengan session token
- XSS Prevention (htmlspecialchars)
- Secure Slug dengan random_bytes()
- Font kustom dengan word-wrap otomatis
- WhatsApp share button

## Troubleshooting

### Permission Error
```bash
chmod -R 777 /home/daniarsyah/my-php-app/src/kartu-lebaran/assets/generated
```

### Container tidak start
```bash
podman-compose logs ecard-web
podman-compose logs ecard-db
```

### Stop containers
```bash
podman-compose down
```

## Lisensi

MIT License - E-Card Lebaran 1447 H
