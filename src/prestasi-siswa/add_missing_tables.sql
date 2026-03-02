-- Add missing tables for Prestasi Siswa application

-- Table: guru
CREATE TABLE IF NOT EXISTS `guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(50) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table: prestasi_guru
CREATE TABLE IF NOT EXISTS `prestasi_guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guru_id` int NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `jenis_prestasi` enum('akademik','non-akademik','penelitian','kompetisi') NOT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `penyelenggara` varchar(255) DEFAULT NULL,
  `foto_sertifikat` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft','archived') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guru_id` (`guru_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table: prestasi_sekolah
CREATE TABLE IF NOT EXISTS `prestasi_sekolah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_prestasi` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tingkat` enum('internasional','nasional','provinsi','kota','kecamatan','sekolah') NOT NULL,
  `peringkat` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `penyelenggara` varchar(255) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft','archived') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table: alumni_ptn
CREATE TABLE IF NOT EXISTS `alumni_ptn` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `jenis` enum('ptn','pts','kerja') NOT NULL,
  `nama_perguruan` varchar(255) DEFAULT NULL,
  `nama_perusahaan` varchar(255) DEFAULT NULL,
  `fakultas` varchar(150) DEFAULT NULL,
  `prodi` varchar(150) DEFAULT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `status_publikasi` enum('published','draft','archived') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Add foreign key constraints
ALTER TABLE `prestasi_guru` ADD CONSTRAINT `prestasi_guru_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;
ALTER TABLE `alumni_ptn` ADD CONSTRAINT `alumni_ptn_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

-- Add status_publikasi column to existing tables if not exists
-- This is handled by ALTER TABLE in admin/index.php already
