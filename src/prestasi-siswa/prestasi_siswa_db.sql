-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Waktu pembuatan: 28 Feb 2026 pada 03.18
-- Versi server: 8.0.45
-- Versi PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `prestasi_siswa_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(150) DEFAULT NULL,
  `role` enum('super_admin','admin') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$12$.hiqEyEhV5QcoL/HIZSK0ORmBI79515Ni7O0qsjrDTuZG69LptRYO', 'Administrator', 'super_admin', '2026-02-28 03:07:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `admin_id` int DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `details` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lomb`
--

CREATE TABLE `lomb` (
  `id` int NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `deskripsi` text,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `Penyelenggara` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi`
--

CREATE TABLE `prestasi` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `jenis_prestasi` enum('akademik','non-akademik') NOT NULL,
  `jenis_peserta` enum('perorangan','kelompok') DEFAULT 'perorangan',
  `nama_tim` varchar(255) DEFAULT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` enum('1','2','3','harapan','finalis','peserta') NOT NULL,
  `tanggal` date NOT NULL,
  `Penyelenggara` varchar(255) DEFAULT NULL,
  `foto_sertifikat` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft','archived') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `prestasi`
--

INSERT INTO `prestasi` (`id`, `siswa_id`, `nama_lomba`, `jenis_prestasi`, `jenis_peserta`, `nama_tim`, `tingkat`, `peringkat`, `tanggal`, `Penyelenggara`, `foto_sertifikat`, `deskripsi`, `status_publikasi`, `created_at`, `updated_at`) VALUES
(1, 1, 'Olimpiade Matematika Nasional', 'akademik', 'perorangan', NULL, 'nasional', '2', '2024-03-15', 'Kemendikbudristek', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(2, 1, 'Lomba Sains Nasional', 'akademik', 'perorangan', NULL, 'provinsi', '1', '2024-02-20', 'Dinas Pendidikan Prov', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(3, 2, 'Lomba Seni Tari Regional', 'non-akademik', 'perorangan', NULL, 'nasional', '1', '2024-04-10', 'Kemendikbudristek', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(4, 3, 'Olimpiade Fisika', 'akademik', 'perorangan', NULL, 'internasional', 'harapan', '2024-01-25', 'International Physics Olympiad', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(5, 4, 'Lomba Basket Putri', 'non-akademik', 'perorangan', NULL, 'kota', '1', '2024-05-01', 'Dispora Kota', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(6, 5, 'Lomba Debat Bahasa Inggris', 'akademik', 'perorangan', NULL, 'provinsi', '2', '2024-03-28', 'Dinas Pendidikan Prov', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(7, 2, 'Lomba Melukis', 'non-akademik', 'perorangan', NULL, 'kecamatan', '1', '2024-06-15', 'Dinas Pendidikan Kec', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(8, 3, 'Olimpiade Kimia', 'akademik', 'perorangan', NULL, 'nasional', '3', '2024-04-22', 'Kemendikbudristek', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(9, 4, 'Lomba Piano', 'non-akademik', 'perorangan', NULL, 'nasional', 'finalis', '2024-07-01', 'Yayasan Musik Indonesia', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(10, 1, 'Lomba Catur', 'non-akademik', 'perorangan', NULL, 'sekolah', '1', '2024-08-10', 'SMAN 1', NULL, NULL, 'published', '2026-02-28 03:07:59', '2026-02-28 03:07:59'),
(11, 1, 'LKBB PROSITION 8', 'non-akademik', 'kelompok', 'Paskibra', 'provinsi', '1', '2025-09-15', 'KPM IKIP Siliwangi', 'prestasi_69a25e60cd01b.png', '🏆 Juara 1 Bina\r\n🏆 Juara 1 Danton Terbaik\r\n🥈 Juara 2 Pra Muda', 'published', '2026-02-28 03:17:52', '2026-02-28 03:17:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nama_siswa`, `kelas`, `foto`, `created_at`) VALUES
(1, '12345', 'Ahmad Fauzi', 'X IPA 1', NULL, '2026-02-28 03:07:59'),
(2, '12346', 'Siti Aminah', 'X IPA 2', NULL, '2026-02-28 03:07:59'),
(3, '12347', 'Budi Santoso', 'XI IPA 1', NULL, '2026-02-28 03:07:59'),
(4, '12348', 'Dewi Lestari', 'XI IPS 1', NULL, '2026-02-28 03:07:59'),
(5, '12349', 'Rendi Pratama', 'XII IPA 1', NULL, '2026-02-28 03:07:59');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indeks untuk tabel `lomb`
--
ALTER TABLE `lomb`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lomb`
--
ALTER TABLE `lomb`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `prestasi`
--
ALTER TABLE `prestasi`
  ADD CONSTRAINT `prestasi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
