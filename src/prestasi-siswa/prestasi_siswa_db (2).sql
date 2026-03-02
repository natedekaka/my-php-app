-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Waktu pembuatan: 02 Mar 2026 pada 01.39
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
DROP TABLE IF EXISTS `admins`;
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
-- Struktur dari tabel `alumni_ptn`
--

CREATE TABLE `alumni_ptn` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `nama_perguruan` varchar(200) DEFAULT NULL,
  `jenis` enum('ptn','pts','kerja') DEFAULT 'ptn',
  `fakultas` varchar(150) DEFAULT NULL,
  `prodi` varchar(150) DEFAULT NULL,
  `nama_perusahaan` varchar(200) DEFAULT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `alumni_ptn`
--

INSERT INTO `alumni_ptn` (`id`, `siswa_id`, `nama_perguruan`, `jenis`, `fakultas`, `prodi`, `nama_perusahaan`, `tahun_ajaran`, `foto`, `deskripsi`, `status_publikasi`, `created_at`) VALUES
(2, 19, 'UNPAD', 'ptn', 'Kedokteran', 'Kedodoran Umum', '', '2024/2025', 'alumni_69a4e261bc87d.png', NULL, 'published', '2026-03-02 01:05:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id`, `nip`, `nama_guru`, `mapel`, `created_at`) VALUES
(3, '198004052022211004', 'Daniarsyah', 'Informatika', '2026-03-02 01:01:02');

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
  `siswa_id` int DEFAULT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `jenis_prestasi` enum('akademik','non-akademik') NOT NULL,
  `jenis_peserta` enum('perorangan','kelompok') DEFAULT 'perorangan',
  `nama_tim` varchar(255) DEFAULT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` varchar(20) DEFAULT NULL,
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
(17, 8, 'Pekan Olimpiade Sains Nasional (PONS) tahun 2026', 'akademik', 'perorangan', '', 'nasional', 'perunggu', '2026-01-19', 'Pekan Olimpiade Sains Nasional (PONS) tahun 2026', 'prestasi_69a3e427d5639.png', 'Pekan Olimpiade Sains Nasional (PONS) tahun 2026', 'published', '2026-03-01 07:00:55', '2026-03-01 07:00:55'),
(18, 9, 'Olimpiade Sains Tingkat Nasional NSSO 2025', 'akademik', 'perorangan', '', 'nasional', 'perak', '2026-01-19', 'Olimpiade Sains Tingkat Nasional NSSO 2025', 'prestasi_69a3e63fd586c.png', 'Olimpiade Sains Tingkat Nasional NSSO 2025', 'published', '2026-03-01 07:09:51', '2026-03-01 07:09:51'),
(20, 7, 'Indonesian Student Competition', 'akademik', 'perorangan', '', 'nasional', 'perunggu', '2026-01-19', 'Indonesian Student Competition', 'prestasi_69a3ea8899805.png', 'Indonesian Student Competition', 'published', '2026-03-01 07:28:08', '2026-03-01 07:28:08'),
(21, 10, 'Bandung Junior Fun Swim Champioship 2026', 'non-akademik', 'perorangan', '', 'provinsi', '3', '2026-01-19', 'Bandung Junior Fun Swim Champioship 2026', 'prestasi_69a3ed850c6e7.png', 'Bandung Junior Fun Swim Champioship 2026', 'published', '2026-03-01 07:40:53', '2026-03-01 07:40:53'),
(22, 12, 'Event Pekan Sains dan Bahasa Nasional Bidang Studi Fisika', 'akademik', 'perorangan', '', 'provinsi', '1', '2026-01-12', 'Event Pekan Sains dan Bahasa Nasional Bidang Studi Fisika', 'prestasi_69a3f480b8e05.png', 'Event Pekan Sains dan Bahasa Nasional Bidang Studi Fisika', 'published', '2026-03-01 08:10:40', '2026-03-01 08:10:40'),
(23, 13, 'Kompetisi Akademik Ilmu Sosial IV Bidang Studi Bahasa Indonesia', 'akademik', 'perorangan', '', 'sekolah', 'perak', '2026-01-12', 'Kompetisi Akademik Ilmu Sosial IV Bidang Studi Bahasa Indonesia', 'prestasi_69a3f568ab113.png', 'Kompetisi Akademik Ilmu Sosial IV Bidang Studi Bahasa Indonesia', 'published', '2026-03-01 08:14:32', '2026-03-01 08:14:32'),
(24, 14, 'Olimpiade Prestasi Indonesia Bidang Studi Bahasa Inggris', 'akademik', 'perorangan', '', 'sekolah', 'emas', '2026-01-12', 'Olimpiade Prestasi Indonesia Bidang Studi Bahasa Inggris', 'prestasi_69a3f6794313c.png', 'Olimpiade Prestasi Indonesia Bidang Studi Bahasa Inggris', 'published', '2026-03-01 08:19:05', '2026-03-01 08:19:05'),
(25, 15, 'Kejuaraan Cianjur Open 2025 Kategori Ganda Remaja Campuran (GRC) Cabang Olahraga Bulu Tangkis', 'akademik', 'perorangan', '', 'provinsi', '3', '2026-01-12', 'Kejuaraan Cianjur Open 2025 Kategori Ganda Remaja Campuran (GRC) Cabang Olahraga Bulu Tangkis', 'prestasi_69a3f7d934648.png', 'Kejuaraan Cianjur Open 2025 Kategori Ganda Remaja Campuran (GRC) Cabang Olahraga Bulu Tangkis', 'published', '2026-03-01 08:24:57', '2026-03-01 08:24:57'),
(28, NULL, 'Rajawali Elite Cup 2025 Se-Jawa Barat', 'non-akademik', 'kelompok', 'Ganda Putera Bulu Tangkis', 'provinsi', '3', '2025-12-18', 'Rajawali Elite Cup 2025 Se-Jawa Barat', 'prestasi_69a3f9dfc7dc4.png', '✨ Rajendra Adithya Abdullah & Muhamad Fahri Rizwan\r\n🥉 Juara 3 – Rajawali Elite Cup 2025 Se-Jawa Barat', 'published', '2026-03-01 08:33:35', '2026-03-01 08:33:35'),
(30, 16, 'Kompetisi Pelajar Cerdas Indonesia Bidang Studi Sejarah SMA', 'akademik', 'perorangan', '', 'sekolah', 'perunggu', '2025-12-01', 'Kompetisi Pelajar Cerdas Indonesia Bidang Studi Sejarah SMA', 'prestasi_69a40e9cb9859.png', 'Kompetisi Pelajar Cerdas Indonesia\r\nBidang Studi Sejarah SMA', 'published', '2026-03-01 10:02:04', '2026-03-01 10:02:04'),
(34, 7, 'Brainy Brilliance Olympiad - Bidang Bahasa Inggris', 'akademik', 'perorangan', '', 'provinsi', 'perunggu', '2025-12-18', 'Brainy Brilliance Olympiad - Bidang Bahasa Inggris', 'prestasi_69a4137ad05aa.png', 'Brainy Brilliance Olympiad - Bidang Bahasa Inggris', 'published', '2026-03-01 10:22:50', '2026-03-01 10:22:50'),
(35, 17, 'Olimpiade Kimia Tingkat Provinsi', 'akademik', 'perorangan', '', 'provinsi', '1', '2025-11-25', 'Olimpiade Kimia Tingkat Nasional yang diselenggarakan oleh Platform Edukasi Pelajar: University ID Education', 'prestasi_69a41417c8794.png', 'Olimpiade Kimia Tingkat Nasional yang diselenggarakan oleh Platform Edukasi Pelajar: University ID Education', 'published', '2026-03-01 10:25:27', '2026-03-01 10:25:27'),
(36, 17, 'Olimpiade Kimia Tingkat Nasional', 'akademik', 'perorangan', '', 'nasional', '1', '2025-11-25', 'Olimpiade Kimia Tingkat Nasional yang diselenggarakan oleh Platform Edukasi Pelajar: University ID Education', 'prestasi_69a41505477af.png', 'Olimpiade Kimia Tingkat Nasional yang diselenggarakan oleh Platform Edukasi Pelajar: University ID Education', 'published', '2026-03-01 10:29:25', '2026-03-01 10:29:25'),
(37, 18, 'Pasanggiri Pencak Silat Cimahi Open 2', 'non-akademik', 'perorangan', '', 'kota', '2', '2025-11-25', 'Kota Cimahi', 'prestasi_69a41609cae42.png', 'Juara 2 Tunggal Remaja Putri dalam acara Pasanggiri Pencak Silat Cimahi Open 2 Pusaka Gagak Lumayung Tahun 2025', 'published', '2026-03-01 10:33:45', '2026-03-01 10:33:45'),
(38, NULL, 'Commnisura X Interferon 2025', 'akademik', 'kelompok', 'Lailya Khairunisya & Annida Nurafifah', 'provinsi', '3', '2025-11-25', 'Commnisura X Interferon 2025', 'prestasi_69a4db42915d1.png', 'Commnisura X Interferon 2025', 'published', '2026-03-02 00:35:14', '2026-03-02 00:35:14'),
(39, NULL, 'Lomba Debat Tingkat Nasional dalam kegiatan Civic Education II dengan Tema “Dari Tradisi menuju Prestasi: Mewujudkan Generasi Cerdas dan Aktif', 'akademik', 'kelompok', 'Silfia Aviatun Nisa, Savinna Challita, Nazwa Agniatun Zahra', 'provinsi', '1', '2025-11-25', 'Lomba Debat Tingkat Nasional dalam kegiatan Civic Education II dengan Tema “Dari Tradisi menuju Prestasi: Mewujudkan Generasi Cerdas dan Aktif', 'prestasi_69a4e014daf89.png', 'Lomba Debat Tingkat Nasional dalam kegiatan Civic Education II dengan Tema “Dari Tradisi menuju Prestasi: Mewujudkan Generasi Cerdas dan Aktif', 'published', '2026-03-02 00:55:48', '2026-03-02 00:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi_guru`
--

CREATE TABLE `prestasi_guru` (
  `id` int NOT NULL,
  `guru_id` int NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `jenis_prestasi` varchar(50) NOT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` varchar(20) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `Penyelenggara` varchar(255) DEFAULT NULL,
  `foto_sertifikat` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `prestasi_guru`
--

INSERT INTO `prestasi_guru` (`id`, `guru_id`, `nama_lomba`, `jenis_prestasi`, `tingkat`, `peringkat`, `tanggal`, `Penyelenggara`, `foto_sertifikat`, `deskripsi`, `status_publikasi`, `created_at`) VALUES
(3, 3, 'Buka Puasa Cepat', 'kompetisi', 'internasional', '1', '2026-03-02', 'MBG', 'guru_69a4e5634d697.png', 'MBG. dibuka aya sanguan, dibuka aya laukan, dibuka aya sayuran susu jeung buah-buahan', 'published', '2026-03-02 01:18:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi_sekolah`
--

CREATE TABLE `prestasi_sekolah` (
  `id` int NOT NULL,
  `nama_prestasi` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` varchar(20) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `Penyelenggara` varchar(255) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `prestasi_sekolah`
--

INSERT INTO `prestasi_sekolah` (`id`, `nama_prestasi`, `kategori`, `tingkat`, `peringkat`, `tanggal`, `Penyelenggara`, `foto_bukti`, `deskripsi`, `status_publikasi`, `created_at`) VALUES
(3, 'Sekolah Adiwiyata', 'lingkungan', 'nasional', '1', '2026-03-02', 'Kementerian Lingkungan Hidup', 'sekolah_69a4e58f7a805.png', 'Kementerian Lingkungan Hidup Konoha', 'published', '2026-03-02 01:19:11');

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
(7, '232410355', 'SULTHON ZULFIKAR', 'XII-2', NULL, '2026-03-01 06:43:07'),
(8, '232410244', 'SHEVINA NASYA ARIA PUTRI', 'XII-8', NULL, '2026-03-01 06:59:01'),
(9, '232410064', 'ROISIYAH SABILA', 'XII-2', NULL, '2026-03-01 07:07:55'),
(10, '252610215', 'MARSYA SEVYANA', 'X-5', NULL, '2026-03-01 07:13:23'),
(12, '232410186', 'DIAZ HUGO ANARGYA ', 'XII-3', NULL, '2026-03-01 08:08:42'),
(13, '232410293', 'DHIAZ ANGGI ROHMATULLAH', 'XII-10', NULL, '2026-03-01 08:13:18'),
(14, '242510055', 'LAKEISHA AZIZAH', 'XI-1', NULL, '2026-03-01 08:17:29'),
(15, '252610222', 'NAURA ADELIA FRANATA', 'X-5', NULL, '2026-03-01 08:22:38'),
(16, '252610002', 'AIRA NAURAH ZAIDA', 'X-1', NULL, '2026-03-01 10:00:42'),
(17, '232410278', 'RIFHANI TSALSA SABILAH', 'XII-5', NULL, '2026-03-01 10:05:54'),
(18, '232410245', 'SINTA LAORA', 'XII-10', NULL, '2026-03-01 10:10:57'),
(19, '1212', 'Natedekaka', 'XII-1', NULL, '2026-03-02 01:04:27');

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
-- Indeks untuk tabel `alumni_ptn`
--
ALTER TABLE `alumni_ptn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

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
-- Indeks untuk tabel `prestasi_guru`
--
ALTER TABLE `prestasi_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `prestasi_sekolah`
--
ALTER TABLE `prestasi_sekolah`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT untuk tabel `alumni_ptn`
--
ALTER TABLE `alumni_ptn`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `prestasi_guru`
--
ALTER TABLE `prestasi_guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `prestasi_sekolah`
--
ALTER TABLE `prestasi_sekolah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `alumni_ptn`
--
ALTER TABLE `alumni_ptn`
  ADD CONSTRAINT `alumni_ptn_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

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

--
-- Ketidakleluasaan untuk tabel `prestasi_guru`
--
ALTER TABLE `prestasi_guru`
  ADD CONSTRAINT `prestasi_guru_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
