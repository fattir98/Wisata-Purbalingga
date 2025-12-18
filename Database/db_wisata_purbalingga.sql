-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2025 at 04:46 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_wisata_purbalingga`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_wisata`
--

CREATE TABLE `booking_wisata` (
  `id_booking` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_wisata` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal_kunjung` date NOT NULL,
  `jumlah_orang` int NOT NULL,
  `total_harga` int DEFAULT NULL,
  `catatan` text,
  `status` enum('pending','approved','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bukti_bayar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_wisata`
--

INSERT INTO `booking_wisata` (`id_booking`, `id_user`, `id_wisata`, `nama`, `no_hp`, `tanggal_kunjung`, `jumlah_orang`, `total_harga`, `catatan`, `status`, `created_at`, `bukti_bayar`) VALUES
(1, NULL, 1, 'Ayla', '08123456', '2025-12-11', 3, NULL, '', 'pending', '2025-12-10 09:53:23', NULL),
(12, 2, 1, 'Ayla', '0859787654', '2025-12-21', 3, 75000, '', 'approved', '2025-12-11 12:31:58', NULL),
(13, 3, 1, 'yoyo', '123456', '2025-12-30', 2, 50000, '', 'cancelled', '2025-12-11 14:54:47', NULL),
(14, 2, 4, 'Ayla', '089123456', '2025-12-20', 1, 23000, '', 'cancelled', '2025-12-12 01:28:30', NULL),
(15, 2, 3, 'Ayla', '08123456', '2025-12-13', 1, 20000, '', 'cancelled', '2025-12-12 01:34:29', NULL),
(16, 5, 3, 'fattir', '08123456', '2025-11-12', 1, 20000, '', 'cancelled', '2025-12-12 03:27:51', NULL),
(17, 6, 2, 'aa', '08123456', '2025-12-01', 1, 15000, 'jjjj', 'cancelled', '2025-12-12 07:23:55', NULL),
(18, 5, 3, 'fattir', '122', '2005-02-12', 2, 40000, '', 'cancelled', '2025-12-13 14:24:00', NULL),
(19, 5, 3, 'fattir', '28', '2006-12-28', 2, 40000, '', 'pending', '2025-12-13 17:46:43', NULL),
(20, 5, 3, 'fattir', '28', '2006-12-28', 2, 40000, '', 'cancelled', '2025-12-13 17:47:57', NULL),
(21, 5, 6, 'fattir', '2324', '2025-12-14', 2, 10000, '', 'pending', '2025-12-13 17:58:37', NULL),
(22, 5, 5, 'fattir', '12', '2025-12-15', 10, 100000, '', 'approved', '2025-12-13 18:06:58', NULL),
(23, 5, 6, 'fattir', '13', '2025-12-14', 1, 5000, '', 'pending', '2025-12-13 18:10:05', NULL),
(24, 5, 6, 'fattir', '122', '2025-12-14', 2, 10000, '', 'pending', '2025-12-13 19:08:20', NULL),
(25, 5, 6, 'fattir', '122', '2025-12-14', 2, 10000, '', 'pending', '2025-12-13 19:08:38', 'BUKTI-25-693dba7e988e7.png'),
(26, 5, 6, 'fattir', '12', '2025-12-15', 4, 20000, '', 'approved', '2025-12-13 19:19:53', 'BUKTI-26-693dbc64c087a.jpg'),
(27, 5, 5, 'fattir', '12345', '2025-12-15', 2, 20000, '', 'cancelled', '2025-12-13 19:25:08', 'BUKTI-27-693dbda3583ee.jpg'),
(28, 1, 5, 'Admin Utama', '12', '2025-12-15', 2, 20000, '', 'pending', '2025-12-13 19:26:41', NULL),
(29, 5, 6, 'fattir', '12', '2025-12-14', 2, 10000, '', 'cancelled', '2025-12-13 19:32:40', NULL),
(30, 5, 10, 'fattir', '12', '2025-12-14', 1, 0, '', 'cancelled', '2025-12-14 15:30:18', NULL),
(31, 5, 10, 'fattir', '12', '2025-12-15', 1, 0, '', 'approved', '2025-12-14 15:30:47', NULL),
(32, 6, 10, 'aa', '12', '2025-12-14', 1, 0, '', 'approved', '2025-12-14 15:39:04', 'BUKTI-32-693edc5725c8e.png'),
(33, 5, 1, 'fattir', '233', '2025-12-17', 1, 25000, '', 'pending', '2025-12-17 04:21:41', 'BUKTI-33-694230125b56e.png'),
(34, 5, 10, 'fattir', '082831', '2025-12-17', 3, 0, '', 'pending', '2025-12-17 04:34:19', NULL),
(35, 5, 1, 'fattir', '23', '2025-12-19', 2, 50000, '', 'pending', '2025-12-17 04:35:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengunjung') NOT NULL DEFAULT 'pengunjung',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin Utama', 'admin@wisata.com', 'admin123', 'admin', '2025-12-04 11:05:20'),
(2, 'Ayla', 'ayla@ensi', '123456', 'pengunjung', '2025-12-11 19:13:33'),
(3, 'yoyo', 'yyyy@oooo', 'yoyo', 'pengunjung', '2025-12-11 21:53:45'),
(4, 'Ibnu', 'narf@gmail', 'ibnunarf', 'pengunjung', '2025-12-12 07:11:21'),
(5, 'fattir', 'fattir@gmail.com', '123456', 'pengunjung', '2025-12-12 10:25:16'),
(6, 'aa', 'aa@wisata.com', '123', 'pengunjung', '2025-12-12 14:22:28');

-- --------------------------------------------------------

--
-- Table structure for table `wisata`
--

CREATE TABLE `wisata` (
  `id_wisata` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text,
  `alamat` text,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga_tiket` int DEFAULT '0',
  `fasilitas` text,
  `link_maps` text,
  `views` int DEFAULT '0',
  `foto` varchar(255) DEFAULT NULL,
  `wa_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wisata`
--

INSERT INTO `wisata` (`id_wisata`, `nama`, `deskripsi`, `alamat`, `kecamatan`, `kategori`, `harga_tiket`, `fasilitas`, `link_maps`, `views`, `foto`, `wa_number`, `created_at`, `latitude`, `longitude`) VALUES
(1, 'Owabong Waterpark', 'Wisata air keluarga dengan wahana terlengkap dan air alami.', 'Jalan Raya Owabong No.1, Dusun 2, Bojongsari, Kabupaten Purbalingga 53362', 'Bojongsari', 'Keluarga', 25000, 'Kolam Renang, Waterboom, Foodcourt', 'https://goo.gl/maps/owabong', 1290, 'owabong.jpg', '6281122334455', '2025-12-04 11:05:20', '-7.349477', '109.349697'),
(2, 'Sanggaluri Park', 'Taman edukasi reptil, uang, dan wayang.', 'Jalan Raya Taman Reptile, Dusun II, Kutasari, Kec. Kutasari, Kabupaten Purbalingga, Jawa Tengah', 'Kutasari', 'Edukasi', 15000, 'Museum, Taman Reptil', 'https://goo.gl/maps/sanggaluri', 862, 'sanggaluri.jpeg', '6281234567890', '2025-12-04 11:05:20', '-7.356655', '109.331149'),
(3, 'Golaga (Goa Lawa)', 'Wisata goa alam yang sejuk dan eksotis.', 'Desa Siwarak RT 01/RW 07, Karangreja, Dusun IV, Siwarak', 'Karangreja', 'Alam', 20000, 'Cafe Goa, Spot Foto', 'https://goo.gl/maps/golaga', 1134, 'golaga.jpg', '6289876543210', '2025-12-04 11:05:20', '-7.228477', '109.318115'),
(4, 'Purbasari Pancuran Mas', 'Purbasari Pancuran Mas adalah taman wisata pendidikan di Purbalingga yang menawarkan akuarium besar, mini zoo, restoran, dan wahana permainan seru.', ' Jl. Raya Purbayasa, RT.03/RW.02, Area Sawah, Purbayasa, Kec. Padamara, Kabupaten Purbalingga, Jawa Tengah', 'Padamara', 'Edukasi', 23000, 'akuarium besar, mini zoo, restoran, dan wahana permainan seru', 'https://maps.app.goo.gl/sccd39drH88yepPEA', 1114, 'purbasari.jpg', NULL, '2025-12-09 20:41:41', '-7.375841', '109.312199'),
(5, 'Dlas Lembah Asri', 'Wisata alam kebun stroberi, dinoland, dan hutan pinus yang sejuk.', 'Jalan Raya Serang, Krajan, Serang, Kecamatan Karangreja, Kabupaten Purbalingga, Jawa Tengah', 'Karangreja', 'Alam', 10000, 'Greenhouse, Strawbery, Dino Land, Taman Bunga dan Taman Kelinci, Berkeliling di Alam Terbuka', 'https://www.google.com/maps/search/D\'Las+Lembah+Asri+Purbalingga', 878, 'D\'las.jpg', '6281229334485', '2025-12-09 21:06:45', '-7.242385', '109.291161'),
(6, 'Curug Sumba', 'Air terjun tersembunyi yang asri dan airnya jernih.', 'Kemojing, Desa Tlahab, Karangreja, Kemejing, Tlahab Kidul', 'Karangreja', 'Alam', 5000, 'Trekking yang Mudah, Self Healing Bersama Alam, Air Terjun Dalam 1 Waktu, Kolam Renang, Spot Foto', 'https://www.google.com/maps/search/Curug+Sumba+Purbalingga', 328, 'curug_sumba.jpg', '-', '2025-12-09 20:41:41', '-7.270726', '109.346861'),
(7, 'Situ Tirta Marta', 'Telaga bening viral untuk foto underwater yang jernih.', 'Situ Tirta Marta terletak di Dusun I, Desa Karangcegak, Kecamatan Kutasari, Kabupaten Purbalingga, Jawa Tengah', 'Kutasari', 'Alam', 10000, 'Fotografi Profesional yang Siap Memfoto dibawah Air, Bersantai di Permukaan Danau, Berfoto di bawah Air, Berkemah di Dasar Danau', 'https://www.google.com/maps/search/Situ+Tirta+Marta+Purbalingga', 803, 'tirta_marta.jpg', '-', '2025-12-09 20:41:41', '-7.333727', '109.313740'),
(8, 'Masjid Cheng Ho', 'Masjid unik dengan arsitektur khas Tionghoa.', 'Desa Selaganggeng, Mrebet', 'Mrebet', 'Religi', 0, 'Desain dan Arsitektur Khas Tiongkok, Destinasi Wisata Religi yang Instagenic, Rest Area', 'https://www.google.com/maps/search/Masjid+Cheng+Ho+Purbalingga', 454, 'cheng_ho.jpg', '-', '2025-12-09 20:41:41', '-7.317611', '109.362080'),
(9, 'Museum Soegarda', 'Museum sejarah untuk mengenal budaya Purbalingga.', 'Jl. Purbalingga-Klampok No.142, Purbalingga Lor', 'Purbalingga', 'Sejarah', 2000, 'Benda-Benda Budaya Prasejarah, Peninggalan Penguasaan atau Bupati Purbalingga, Peralatan untuk Keperluan Harian', 'https://www.google.com/maps/search/Museum+Soegarda+Purbalingga', 226, 'museum.jpg', '0281891616', '2025-12-09 20:41:41', '-7.388618', '109.363038'),
(10, 'Alun-Alun Purbalingga', 'Pusat kota yang ramai dengan kuliner malam dan air mancur.', 'Jl. jend. Sudirman, Purbalinggga Lor', 'Purbalingga', 'Keluarga', 0, 'Taman Alun-alun Purbalingga yang Unik, Pusat Kuliner Malam, Wahana Permainan Anak-anak, Kawasan Penuh Spot Foto', 'https://www.google.com/maps/search/Alun-Alun+Purbalingga', 5031, 'alun-alun.jpg', '-', '2025-12-09 20:41:41', '-7.389473', '109.362898');

-- --------------------------------------------------------

--
-- Table structure for table `wisata_galeri`
--

CREATE TABLE `wisata_galeri` (
  `id_galeri` int NOT NULL,
  `id_wisata` int NOT NULL,
  `nama_foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wisata_galeri`
--

INSERT INTO `wisata_galeri` (`id_galeri`, `id_wisata`, `nama_foto`) VALUES
(1, 1, 'galeri_owabong_1.jpg'),
(2, 1, 'galeri_owabong_2.jpg'),
(3, 2, 'galeri_sanggaluri_1.jpg'),
(4, 2, 'galeri_sanggaluri_2.jpg'),
(5, 3, 'galeri_golaga_1.jpg'),
(6, 3, 'galeri_golaga_2.jpg'),
(7, 4, 'galeri_purbasari_1.jpg'),
(8, 4, 'galeri_purbasari_2.jpg'),
(9, 5, 'galeri_dlas_1.jpg'),
(10, 5, 'galeri_dlas_2.jpg'),
(11, 6, 'galeri_curug_1.jpg'),
(12, 6, 'galeri_curug_2.jpg'),
(13, 7, 'galeri_tirta_1.jpg'),
(14, 7, 'galeri_tirta_2.jpg'),
(15, 8, 'galeri_chengho_1.jpg'),
(16, 8, 'galeri_chengho_2.jpg'),
(17, 9, 'galeri_museum_1.jpg'),
(18, 9, 'galeri_museum_2.jpg'),
(19, 10, 'galeri_alun_1.jpg'),
(20, 10, 'galeri_alun_2.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_wisata`
--
ALTER TABLE `booking_wisata`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_wisata` (`id_wisata`),
  ADD KEY `fk_booking_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Indexes for table `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id_wisata`);

--
-- Indexes for table `wisata_galeri`
--
ALTER TABLE `wisata_galeri`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `id_wisata` (`id_wisata`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_wisata`
--
ALTER TABLE `booking_wisata`
  MODIFY `id_booking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id_wisata` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wisata_galeri`
--
ALTER TABLE `wisata_galeri`
  MODIFY `id_galeri` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_wisata`
--
ALTER TABLE `booking_wisata`
  ADD CONSTRAINT `booking_wisata_ibfk_1` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`),
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wisata_galeri`
--
ALTER TABLE `wisata_galeri`
  ADD CONSTRAINT `wisata_galeri_ibfk_1` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
