-- =====================================================
-- DATABASE: daftar_hadir_db
-- =====================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS daftar_hadir_db;
USE daftar_hadir_db;

-- =====================================================
-- TABLE: events (Jenis Rapat/Acara)
-- =====================================================
DROP TABLE IF EXISTS events;

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_event VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    aktif ENUM('Y', 'N') DEFAULT 'Y',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample events
INSERT INTO events (nama_event, deskripsi, aktif) VALUES 
('Rapat', 'Rapat biasa', 'Y'),
('Upacara', 'Upacara bendera', 'Y'),
('Siswa', 'Absensi siswa', 'Y');

-- =====================================================
-- TABLE: form_fields (Konfigurasi Form)
-- =====================================================
DROP TABLE IF EXISTS form_fields;

CREATE TABLE form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_field VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL,
    tipe ENUM('text', 'number', 'date', 'select', 'textarea', 'signature') DEFAULT 'text',
    placeholder VARCHAR(255),
    wajib ENUM('Y', 'N') DEFAULT 'N',
    urutan INT DEFAULT 0,
    aktif ENUM('Y', 'N') DEFAULT 'Y',
    event_id INT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default fields
INSERT INTO form_fields (nama_field, label, tipe, placeholder, wajib, urutan, aktif) VALUES 
('nama_lengkap', 'Nama Lengkap', 'text', 'Masukkan nama lengkap', 'Y', 1, 'Y'),
('nip', 'NIP', 'number', 'Masukkan NIP', 'N', 2, 'Y'),
('kategori', 'Kategori', 'select', 'Pilih kategori', 'Y', 3, 'Y'),
('tanggal', 'Tanggal', 'date', '', 'Y', 4, 'Y'),
('keterangan', 'Keterangan', 'textarea', 'Opsional', 'N', 5, 'Y'),
('ttd', 'Tanda Tangan', 'signature', '', 'Y', 6, 'Y');

-- =====================================================
-- TABLE: field_options (Opsi untuk dropdown)
-- =====================================================
DROP TABLE IF EXISTS field_options;

CREATE TABLE field_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    nilai VARCHAR(255) NOT NULL,
    label VARCHAR(255) NOT NULL,
    FOREIGN KEY (field_id) REFERENCES form_fields(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert options for kategori (references events table)
INSERT INTO field_options (field_id, nilai, label) 
SELECT f.id, e.nama_event, e.nama_event 
FROM form_fields f 
CROSS JOIN events e 
WHERE f.nama_field = 'kategori' AND e.aktif = 'Y';

-- =====================================================
-- TABLE: presensi (Data Kehadiran)
-- =====================================================
DROP TABLE IF EXISTS presensi;

CREATE TABLE presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    data_json JSON NOT NULL,
    ttd_file VARCHAR(255),
    waktu DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_waktu (waktu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CATATAN PENGGUNAAN:
-- =====================================================
-- 1. Import file ini ke MySQL: 
--    mysql -u root -p < database.sql
--    
-- 2. Atau jalankan via phpMyAdmin:
--    - Buat database baru bernama "daftar_hadir_db"
--    - Import file ini
--
-- 3. Konfigurasi koneksi (koneksi.php):
--    - $host = 'db:3306' (untuk podman/docker)
--    - $user = 'root'
--    - $pass = 'rootpass'
--    - $db   = 'daftar_hadir_db'
-- =====================================================
