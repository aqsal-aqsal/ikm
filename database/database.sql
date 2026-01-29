-- Database: ikm

CREATE TABLE IF NOT EXISTS `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('BAPENDA','UPPD') NOT NULL DEFAULT 'UPPD',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('SUPERADMIN','ADMIN_PROVINSI','ADMIN_UPPD','OPERATOR') NOT NULL DEFAULT 'OPERATOR',
  `unit_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `fk_users_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `unsur_ikm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama_unsur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `responden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `umur` int(3) NOT NULL,
  `jk` enum('L','P') NOT NULL,
  `pendidikan` varchar(50) NOT NULL,
  `pekerjaan` varchar(100) NOT NULL,
  `waktu_buat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `responden_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `saran` text,
  PRIMARY KEY (`id`),
  KEY `responden_id` (`responden_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `fk_survey_responden` FOREIGN KEY (`responden_id`) REFERENCES `responden` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_survey_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `survey_jawaban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `unsur_ikm_id` int(11) NOT NULL,
  `nilai` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`),
  KEY `unsur_ikm_id` (`unsur_ikm_id`),
  CONSTRAINT `fk_jawaban_survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jawaban_unsur` FOREIGN KEY (`unsur_ikm_id`) REFERENCES `unsur_ikm` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy Data
INSERT INTO `units` (`nama`, `jenis`) VALUES 
('Bapenda Pusat', 'BAPENDA'),
('UPPD Banjarmasin 1', 'UPPD'),
('UPPD Banjarbaru', 'UPPD');

INSERT INTO `unsur_ikm` (`kode`, `nama_unsur`) VALUES 
('U1', 'Persyaratan'),
('U2', 'Prosedur'),
('U3', 'Waktu Pelayanan'),
('U4', 'Biaya/Tarif'),
('U5', 'Produk Spesifikasi Jenis Pelayanan'),
('U6', 'Kompetensi Pelaksana'),
('U7', 'Perilaku Pelaksana'),
('U8', 'Penanganan Pengaduan, Saran dan Masukan'),
('U9', 'Kualitas Sarana dan Prasarana');

-- Password default: admin123 (hash it in real app, using plain text for dummy if hash not generated, but better use standard hash)
-- using simple hash for example or just plain for simplicity if not implementing full auth logic yet. 
-- I will use password_hash('admin123', PASSWORD_BCRYPT) value for safety.
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (standard laravel 'password')
INSERT INTO `users` (`username`, `password`, `role`, `unit_id`) VALUES 
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SUPERADMIN', NULL),
('admin_uppd1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN_UPPD', 2);
