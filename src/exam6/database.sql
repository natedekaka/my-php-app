-- Database Schema untuk Sistem Ujian Online
-- Buat database terlebih dahulu: CREATE DATABASE ujian_online;

USE ujian_online;

-- Tabel Ujian
CREATE TABLE IF NOT EXISTS ujian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul_ujian VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'nonaktif',
    tgl_dibuat DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Soal
CREATE TABLE IF NOT EXISTS soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ujian INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    gambar_pertanyaan VARCHAR(255) DEFAULT NULL,
    opsi_a VARCHAR(255) NOT NULL,
    gambar_a VARCHAR(255) DEFAULT NULL,
    opsi_b VARCHAR(255) NOT NULL,
    gambar_b VARCHAR(255) DEFAULT NULL,
    opsi_c VARCHAR(255) NOT NULL,
    gambar_c VARCHAR(255) DEFAULT NULL,
    opsi_d VARCHAR(255) NOT NULL,
    gambar_d VARCHAR(255) DEFAULT NULL,
    opsi_e VARCHAR(255) NOT NULL,
    gambar_e VARCHAR(255) DEFAULT NULL,
    kunci_jawaban ENUM('a', 'b', 'c', 'd', 'e') NOT NULL,
    poin INT DEFAULT 10,
    FOREIGN KEY (id_ujian) REFERENCES ujian(id) ON DELETE CASCADE
);

-- Tabel Hasil Ujian
CREATE TABLE IF NOT EXISTS hasil_ujian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ujian INT NOT NULL,
    nis VARCHAR(50) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(50) NOT NULL,
    total_skor INT DEFAULT 0,
    waktu_submit DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ujian) REFERENCES ujian(id) ON DELETE CASCADE
);

-- Cara update tabel lama (jalankan jika tabel sudah ada):
-- ALTER TABLE soal 
-- ADD COLUMN gambar_pertanyaan VARCHAR(255) DEFAULT NULL AFTER pertanyaan,
-- ADD COLUMN gambar_a VARCHAR(255) DEFAULT NULL AFTER opsi_a,
-- ADD COLUMN gambar_b VARCHAR(255) DEFAULT NULL AFTER opsi_b,
-- ADD COLUMN gambar_c VARCHAR(255) DEFAULT NULL AFTER opsi_c,
-- ADD COLUMN gambar_d VARCHAR(255) DEFAULT NULL AFTER opsi_d,
-- ADD COLUMN gambar_e VARCHAR(255) DEFAULT NULL AFTER opsi_e;

-- Tabel Admin/User
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (username: admin, password: admin123)
-- Password di-hash dengan password_hash()
INSERT INTO admin_users (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');
