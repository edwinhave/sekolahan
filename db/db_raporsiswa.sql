-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2026 at 12:28 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_raporsiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_siswa`
--

CREATE TABLE `data_siswa` (
  `nisn` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kelas` int NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `password` varchar(10) DEFAULT NULL,
  `level` int NOT NULL,
  `status_akun` enum('Waiting','Approved','Rejected') DEFAULT 'Waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_siswa`
--

INSERT INTO `data_siswa` (`nisn`, `nama`, `email`, `kelas`, `alamat`, `password`, `level`, `status_akun`) VALUES
('0011101249', 'Edwin Jhon Vangel S', 'edwin@gmail.com', 12, 'Bandung', 'rahasia', 1, 'Approved'),
('1000000001', 'admin', 'super@example.com', 99, 'ADMIN SUPER', 'superadmin', 2, 'Approved'),
('1010101010', 'PAYUNG', 'payung@gmail.com', 10, 'payung', '123123', 1, 'Waiting'),
('1234512345', 'Edmenang', 'ed@gmail.com', 7, 'menang', '123123', 1, 'Approved'),
('1234567899', 'Edkalah', 'edkalah@gmail.com', 1, 'Jalan Kalah', '12345qwert', 1, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran`
--

CREATE TABLE `kehadiran` (
  `id_kehadiran` int NOT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpha') DEFAULT 'Hadir',
  `semester` enum('Ganjil','Genap') DEFAULT 'Ganjil'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kehadiran`
--

INSERT INTO `kehadiran` (`id_kehadiran`, `nisn`, `tanggal`, `status`, `semester`) VALUES
(1, '0011101249', '2026-05-23', 'Hadir', 'Ganjil'),
(2, '0011101249', '2026-04-23', 'Alpha', 'Ganjil');

-- --------------------------------------------------------

--
-- Table structure for table `komentar_guru`
--

CREATE TABLE `komentar_guru` (
  `id_komentar` int NOT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `judul_komentar` varchar(100) DEFAULT NULL,
  `isi_komentar` text,
  `tanggal_input` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `komentar_guru`
--

INSERT INTO `komentar_guru` (`id_komentar`, `nisn`, `judul_komentar`, `isi_komentar`, `tanggal_input`) VALUES
(1, '1234567899', 'Matematika', 'Belajar lagi\r\n', '2026-05-29');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id_matapelajaran` varchar(2) NOT NULL,
  `matapelajaran` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id_matapelajaran`, `matapelajaran`) VALUES
('1a', 'Pendidikan Agama '),
('1b', 'Pendidikan Pancasila'),
('1c', 'Bahasa Indonesia'),
('1d', 'Bahasa Inggris'),
('1e', 'Matematika'),
('1f', 'IPA'),
('1g', 'IPS'),
('1h', 'Seni Budaya'),
('1i', 'PJOK'),
('1j', 'TIK');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `id_pelanggaran` int NOT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `jenis_pelanggaran` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kategori` enum('Ringan','Sedang','Berat') DEFAULT 'Ringan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tabel_nilai`
--

CREATE TABLE `tabel_nilai` (
  `id_nilai` int NOT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `id_matapelajaran` varchar(2) DEFAULT NULL,
  `pe1` int DEFAULT '0',
  `pe2` int DEFAULT '0',
  `pe3` int DEFAULT '0',
  `pe4` int DEFAULT '0',
  `pe5` int DEFAULT '0',
  `pe6` int DEFAULT '0',
  `pts` int DEFAULT '0',
  `asaj` int DEFAULT '0',
  `semester` enum('Ganjil','Genap') DEFAULT 'Ganjil'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tabel_nilai`
--

INSERT INTO `tabel_nilai` (`id_nilai`, `nisn`, `id_matapelajaran`, `pe1`, `pe2`, `pe3`, `pe4`, `pe5`, `pe6`, `pts`, `asaj`, `semester`) VALUES
(3, '0011101249', '1a', 5, 51, 1, 22, 3, 1, 0, 0, 'Ganjil'),
(4, '1234567899', '1c', 0, 0, 0, 0, 0, 0, 0, 0, 'Ganjil'),
(5, '1234512345', '1h', 0, 0, 0, 0, 0, 0, 0, 0, 'Ganjil'),
(7, '0011101249', '1a', 0, 0, 0, 0, 0, 0, 0, 0, 'Ganjil'),
(8, '0011101249', '1a', 10, 10, 10, 10, 10, 10, 10, 100, 'Ganjil');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_siswa`
--
ALTER TABLE `data_siswa`
  ADD PRIMARY KEY (`nisn`);

--
-- Indexes for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD PRIMARY KEY (`id_kehadiran`),
  ADD KEY `nisn` (`nisn`);

--
-- Indexes for table `komentar_guru`
--
ALTER TABLE `komentar_guru`
  ADD PRIMARY KEY (`id_komentar`),
  ADD KEY `fk_komentar_siswa` (`nisn`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id_matapelajaran`);

--
-- Indexes for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`id_pelanggaran`),
  ADD KEY `fk_pelanggaran_siswa` (`nisn`);

--
-- Indexes for table `tabel_nilai`
--
ALTER TABLE `tabel_nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD KEY `fk_siswa_nilai` (`nisn`),
  ADD KEY `fk_mapel_nilai` (`id_matapelajaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kehadiran`
--
ALTER TABLE `kehadiran`
  MODIFY `id_kehadiran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `komentar_guru`
--
ALTER TABLE `komentar_guru`
  MODIFY `id_komentar` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `id_pelanggaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tabel_nilai`
--
ALTER TABLE `tabel_nilai`
  MODIFY `id_nilai` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD CONSTRAINT `kehadiran_ibfk_1` FOREIGN KEY (`nisn`) REFERENCES `data_siswa` (`nisn`) ON DELETE CASCADE;

--
-- Constraints for table `komentar_guru`
--
ALTER TABLE `komentar_guru`
  ADD CONSTRAINT `fk_komentar_siswa` FOREIGN KEY (`nisn`) REFERENCES `data_siswa` (`nisn`) ON DELETE CASCADE;

--
-- Constraints for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD CONSTRAINT `fk_pelanggaran_siswa` FOREIGN KEY (`nisn`) REFERENCES `data_siswa` (`nisn`) ON DELETE CASCADE;

--
-- Constraints for table `tabel_nilai`
--
ALTER TABLE `tabel_nilai`
  ADD CONSTRAINT `fk_mapel_nilai` FOREIGN KEY (`id_matapelajaran`) REFERENCES `mata_pelajaran` (`id_matapelajaran`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_siswa_nilai` FOREIGN KEY (`nisn`) REFERENCES `data_siswa` (`nisn`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
