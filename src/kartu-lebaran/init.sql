-- E-Card Lebaran 1447 H Database Initialization
-- Database: kartu_lebaran_db

CREATE DATABASE IF NOT EXISTS kartu_lebaran_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kartu_lebaran_db;

CREATE TABLE IF NOT EXISTS kartu_ucapan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    pengirim VARCHAR(100) NOT NULL,
    penerima VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    template_path VARCHAR(255) NOT NULL DEFAULT 'templates/template1.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
