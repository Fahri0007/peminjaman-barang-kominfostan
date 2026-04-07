-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Mar 2026 pada 03.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peminjaman_barang`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'Admin Diskominfostan', 'DISKOMINFOSTAN', '$2y$10$fcLtxqL0QhsHQc4g804rUuukBgn9DZdYpGQm8jB/QD36.iyK3/y56', NULL, '2026-01-30 06:45:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `kondisi` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_serial` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `serial_number`, `satuan`, `lokasi`, `stok`, `kondisi`, `deskripsi`, `is_serial`) VALUES
(6, 'Tang Crimping', NULL, 'unit', 'Live Center', 34, 'Bekas', 'Merk asasaB', 0),
(7, 'Laptop', NULL, 'unit', 'Ruang TIKSAN', 50, 'Baru', '123', 1),
(8, 'LAN', NULL, 'meter', 'Ruang Programmer', 49, 'Baru', '-', 0),
(9, 'RJ45', NULL, 'pcs', 'Live Center', 40, 'Baru', 'ashaas', 0),
(10, 'Monitor', NULL, 'unit', 'Ruang a', 38, 'Bekas (Baik)', 'Merk b', 1),
(11, 'Router', NULL, 'unit', 'A', 36, 'Bekas (Baik)', 'Merk aaa', 1),
(12, 'Server', NULL, 'unit', 'B', 35, 'Bekas (Baik)', 'Merk aaa', 1),
(13, 'Switch', NULL, 'unit', 'C', 45, 'Baru', 'Merk C', 1),
(16, 'Patch Panel', NULL, '', 'V', 46, 'Baru', '-', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `kode_pinjam` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `nama_peminjam` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `nama_dinas` varchar(100) DEFAULT NULL,
  `tgl_pinjam` date DEFAULT NULL,
  `jam_pinjam` time DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `jam_kembali` time DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1,
  `status` enum('menunggu','dipinjam','ditolak','dikembalikan') DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `serial_number` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman_serial`
--

CREATE TABLE `peminjaman_serial` (
  `id` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_serial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `serial_barang`
--

CREATE TABLE `serial_barang` (
  `id_serial` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `status` enum('tersedia','dipinjam','rusak') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `serial_barang`
--

INSERT INTO `serial_barang` (`id_serial`, `id_barang`, `serial_number`, `status`) VALUES
(64, 7, 'LTP-HP-0001', 'tersedia'),
(65, 7, 'LTP-HP-0002', 'tersedia'),
(66, 7, 'LTP-HP-0003', 'tersedia'),
(67, 7, 'LTP-HP-0004', 'tersedia'),
(68, 7, 'LTP-HP-0005', 'tersedia'),
(69, 10, 'MNR-TV-0001', 'tersedia'),
(70, 10, 'MNR-TV-0002', 'tersedia'),
(71, 10, 'MNR-TV-0003', 'tersedia'),
(72, 10, 'MNR-TV-0004', 'tersedia'),
(73, 10, 'MNR-TV-0005', 'tersedia'),
(74, 7, 'LTP-HP-0006', 'tersedia'),
(75, 11, 'RTR-R-0001', 'tersedia'),
(76, 11, 'RTR-R-0002', 'tersedia'),
(77, 11, 'RTR-R-0003', 'tersedia'),
(79, 11, 'RTR-R-0005', 'tersedia'),
(80, 11, 'RTR-R-0006', 'tersedia'),
(81, 11, 'RTR-R-0007', 'tersedia'),
(82, 13, 'STH-S-0001', 'tersedia'),
(83, 13, 'STH-S-0002', 'tersedia'),
(84, 7, 'LTP-HP-0007', 'tersedia'),
(85, 7, 'LTP-HP-0008', 'tersedia'),
(86, 7, 'LTP-HP-0009', 'tersedia'),
(87, 7, 'LTP-HP-0010', 'tersedia'),
(88, 12, 'SVR-0-001', 'tersedia'),
(89, 12, 'SVR-0-0022', 'tersedia'),
(90, 12, 'SVR-0-003', 'tersedia'),
(91, 12, 'SVR-0-004', 'tersedia'),
(92, 12, 'SVR-0-005', 'tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `nama_dinas` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`, `unit_kerja`, `nama_dinas`) VALUES

(1, 'Admin Diskominfo', 'DISKOMINFOSTAN', '$2y$10$fcLtxqL0QhsHQc4g804rUuukBgn9DZdYpGQm8jB/QD36.iyK3/y56', 'admin', 'IT', 'DISKOMINFOSTAN');
--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `peminjaman_serial`
--
ALTER TABLE `peminjaman_serial`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `serial_barang`
--
ALTER TABLE `serial_barang`
  ADD PRIMARY KEY (`id_serial`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT untuk tabel `peminjaman_serial`
--
ALTER TABLE `peminjaman_serial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `serial_barang`
--
ALTER TABLE `serial_barang`
  MODIFY `id_serial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
