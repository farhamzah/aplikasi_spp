-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 01:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spp`
--

-- --------------------------------------------------------

--
-- Table structure for table `cek_pembayaran`
--

CREATE TABLE `cek_pembayaran` (
  `nisn` varchar(10) NOT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `tgl_sekarang` date DEFAULT NULL,
  `status_pembayaran` enum('Belum Lunas','Sudah Lunas') NOT NULL,
  `jumlah_bulan` varchar(5) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `no_telp` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cek_pembayaran`
--

INSERT INTO `cek_pembayaran` (`nisn`, `tgl_terakhir_bayar`, `tgl_sekarang`, `status_pembayaran`, `jumlah_bulan`, `nama`, `no_telp`) VALUES
('0067891235', '2026-05-12', '2026-05-12', 'Belum Lunas', '1', 'Bella Maharani', '081234567891'),
('0067891239', '2026-05-25', '2026-05-25', 'Sudah Lunas', '1', 'Fajar Ramadhan', '081234567895'),
('0067891234', '2026-05-25', '2026-05-25', 'Belum Lunas', '1', 'Andi Saputra', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kelas`
--

CREATE TABLE `tb_kelas` (
  `id_kelas` varchar(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `komp_keahlian` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kelas`
--

INSERT INTO `tb_kelas` (`id_kelas`, `nama_kelas`, `komp_keahlian`) VALUES
('1', 'X RPL 1', 'Rekayasa Perangkat Lunak'),
('2', 'X RPL 2', 'Rekayasa Perangkat Lunak'),
('3', 'XI TKJ 1', 'Teknik Komputer dan Jaringan'),
('4', 'XI MM 1', 'Multimedia'),
('5', 'XII AKL 1', 'Akuntansi dan Keuangan Lembaga'),
('6', 'XII RPL 1', 'Rekayasa Perangkat Lunak');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran`
--

CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` varchar(11) NOT NULL,
  `id_spp` varchar(11) NOT NULL,
  `nisn` varchar(10) NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `batas_pembayaran` date DEFAULT NULL,
  `jumlah_bulan` varchar(10) DEFAULT NULL,
  `status` enum('Belum lunas','Sudah lunas','','') DEFAULT NULL,
  `nominal_bayar` varchar(100) DEFAULT NULL,
  `jumlah_bayar` varchar(40) DEFAULT NULL,
  `kembalian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembayaran`
--

INSERT INTO `tb_pembayaran` (`id_pembayaran`, `id_spp`, `nisn`, `tgl_bayar`, `tgl_terakhir_bayar`, `batas_pembayaran`, `jumlah_bulan`, `status`, `nominal_bayar`, `jumlah_bayar`, `kembalian`) VALUES
('2', '3', '0067891235', '2026-05-12', '2026-05-12', '2026-06-12', '1', 'Sudah lunas', '250000', '300000', '50000'),
('3', '6', '0067891239', '2026-05-25', '2026-05-25', '2026-06-25', '1', 'Sudah lunas', '325000.00', '325000.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_petugas`
--

CREATE TABLE `tb_petugas` (
  `id_petugas` varchar(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nama_petugas` varchar(35) NOT NULL,
  `level` enum('admin','petugas','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_petugas`
--

INSERT INTO `tb_petugas` (`id_petugas`, `username`, `password`, `nama_petugas`, `level`) VALUES
('1', 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin'),
('2', 'siti', '5c2e4a2563f9f4427955422fe1402762', 'Siti Aminah', 'petugas'),
('3', 'budi', '9c5fa085ce256c7c598f6710584ab25d', 'Budi Santoso', 'petugas'),
('4', 'rina', '9a1591b6e5317fb71c6032eedd5c051a', 'Rina Lestari', 'petugas'),
('5', 'dimas', '51947e3cf64ee746b6f2c73d174d525a', 'Dimas Pratama', 'petugas'),
('6', 'ali', '984d8144fa08bfc637d2825463e184fa', 'Ali Mustofa', 'siswa'),
('7', 'farham', 'e9391c66c18ded5f86c56e9a81c1236e', 'Farhamzah', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa`
--

CREATE TABLE `tb_siswa` (
  `nisn` varchar(10) NOT NULL,
  `nis` varchar(8) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `id_kelas` varchar(11) NOT NULL,
  `nama_kelas` varchar(10) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(13) DEFAULT NULL,
  `id_spp` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_siswa`
--

INSERT INTO `tb_siswa` (`nisn`, `nis`, `nama`, `id_kelas`, `nama_kelas`, `alamat`, `no_telp`, `id_spp`) VALUES
('0067891234', '10010001', 'Andi Saputra Sari', '1', 'X RPL 1', 'Jl. Merdeka No. 10', '081234567890', '3'),
('0067891235', '10010002', 'Bella Maharani', '1', 'X RPL 1', 'Jl. Anggrek No. 5', '081234567891', '3'),
('0067891236', '10010003', 'Candra Wijaya', '2', 'X RPL 2', 'Jl. Kenanga No. 8', '081234567892', '3'),
('0067891238', '10010005', 'Eko Prasetyo', '4', 'XI MM 1', 'Jl. Mawar No. 20', '081234567894', '2'),
('0067891239', '10010006', '1111', '6', 'XII RPL 1', 'Jl. Cempaka No. 14', '081234567895', '6');

-- --------------------------------------------------------

--
-- Table structure for table `tb_spp`
--

CREATE TABLE `tb_spp` (
  `id_spp` varchar(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `nominal` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_spp`
--

INSERT INTO `tb_spp` (`id_spp`, `tahun`, `nominal`) VALUES
('1', 2024, '200000.00'),
('2', 2025, '225000.00'),
('3', 2026, '250000.00'),
('4', 2027, '275000.00'),
('5', 2028, '3000'),
('6', 2029, '325000.00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` varchar(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `level` enum('admin','petugas','siswa') NOT NULL,
  `nisn` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `nama_user`, `level`, `nisn`) VALUES
('1', 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin', NULL),
('10', '0067891234', '9ae306974abac09aa8f0ebbe2df15f4e', 'Andi Saputra', 'siswa', '0067891234'),
('2', 'siti', '5c2e4a2563f9f4427955422fe1402762', 'Siti Aminah', 'petugas', NULL),
('3', 'budi', '9c5fa085ce256c7c598f6710584ab25d', 'Budi Santoso', 'petugas', NULL),
('4', 'rina', '9a1591b6e5317fb71c6032eedd5c051a', 'Rina Lestari', 'petugas', NULL),
('5', 'dimas', '51947e3cf64ee746b6f2c73d174d525a', 'Dimas Pratama', 'petugas', NULL),
('6', 'ali', '984d8144fa08bfc637d2825463e184fa', 'Ali Mustofa', 'siswa', NULL),
('7', 'farham', 'e9391c66c18ded5f86c56e9a81c1236e', 'Farhamzah', 'admin', NULL),
('8', '0067891235', 'a903b9b19bc89d46898442ca0fcec540', 'Bella Maharani', 'siswa', '0067891235'),
('9', '0067891239', 'e6a395a0cab248c90b7a2c8072c521ca', 'Fajar Ramadhan', 'siswa', '0067891239');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_kelas`
--
ALTER TABLE `tb_kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indexes for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_spp` (`id_spp`),
  ADD KEY `nisn` (`nisn`);

--
-- Indexes for table `tb_petugas`
--
ALTER TABLE `tb_petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indexes for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`nisn`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_spp` (`id_spp`);

--
-- Indexes for table `tb_spp`
--
ALTER TABLE `tb_spp`
  ADD PRIMARY KEY (`id_spp`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD CONSTRAINT `tb_pembayaran_ibfk_1` FOREIGN KEY (`id_spp`) REFERENCES `tb_spp` (`id_spp`),
  ADD CONSTRAINT `tb_pembayaran_ibfk_2` FOREIGN KEY (`nisn`) REFERENCES `tb_siswa` (`nisn`);

--
-- Constraints for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD CONSTRAINT `tb_siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `tb_kelas` (`id_kelas`),
  ADD CONSTRAINT `tb_siswa_ibfk_2` FOREIGN KEY (`id_spp`) REFERENCES `tb_spp` (`id_spp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
