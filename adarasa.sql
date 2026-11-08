-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 08, 2026 at 05:06 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adarasa`
--

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id_bahan` int NOT NULL,
  `nama_bahan` varchar(255) NOT NULL,
  `jumlah_default` double NOT NULL,
  `satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fk_menu_bahan` int DEFAULT NULL,
  `fk_bahan_stok` int DEFAULT NULL,
  `fk_varian_bahan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id_bahan`, `nama_bahan`, `jumlah_default`, `satuan`, `fk_menu_bahan`, `fk_bahan_stok`, `fk_varian_bahan`) VALUES
(152, 'Cumi-cumi', 75, 'gram', 88, 72, NULL),
(153, 'Beras', 80, 'gram', 88, 73, NULL),
(154, 'Sambel ijo', 4, 'buah', 88, 74, NULL),
(155, 'Garam', 2, 'sendok teh', 88, 75, NULL),
(156, 'Minyak goreng', 10, 'ml', 88, 76, NULL),
(157, 'Air', 100, 'ml', 88, 77, NULL),
(159, 'Kangkung', 100, 'gram', 90, 78, NULL),
(160, 'Cabai merah', 4, 'buah', 90, 79, NULL),
(161, 'Saus tiram', 1, 'bungkus', 90, 80, NULL),
(162, 'Bawang merah', 5, 'buah', 90, 81, NULL),
(163, 'Kecap manis', 1, 'sendok makan', 90, 82, NULL),
(164, 'Wortel', 1, 'buah', 97, 83, NULL),
(165, 'Kubis', 1, 'buah', 97, 84, NULL),
(166, 'Tepung', 500, 'gram', 97, 85, NULL),
(167, 'Cabai', 5, 'buah', 97, 86, NULL),
(168, 'Garam', 3, 'sendok teh', 97, 75, NULL),
(169, 'Pisang', 5, 'buah', 93, 87, NULL),
(170, 'Tepung', 500, 'gram', 93, 85, NULL),
(171, 'Telur', 3, 'buah', 93, 88, NULL),
(172, 'Gula', 100, 'gram', 93, 89, NULL),
(173, 'Vanili', 1, 'buah', 93, 90, NULL),
(174, 'Ayam', 80, 'gram', 89, 91, 47),
(175, 'Ayam', 90, 'gram', 89, 91, 48),
(176, 'Kecap manis', 3, 'sendok makan', 89, 82, 47),
(177, 'Kecap manis', 3, 'sendok makan', 89, 82, 48),
(178, 'Beras', 100, 'gram', 89, 73, 47),
(179, 'Beras', 100, 'gram', 89, 73, 48),
(180, 'Beras', 100, 'gram', 95, 73, NULL),
(181, 'Kunyit', 2, 'buah', 95, 92, NULL),
(182, 'Tempe', 1, 'buah', 95, 93, NULL),
(183, 'Telur', 1, 'buah', 95, 88, NULL),
(184, 'Wortel', 1, 'buah', 95, 83, NULL),
(185, 'Wortel', 1, 'buah', 95, 83, NULL),
(186, 'Tempe', 1, 'buah', 92, 93, NULL),
(187, 'Tepung', 100, 'gram', 92, 85, NULL),
(188, 'Beras', 100, 'gram', 96, 73, NULL),
(189, 'Telur', 3, 'buah', 96, 88, NULL),
(190, 'Cabai merah', 6, 'buah', 96, 79, NULL),
(191, 'Selai nanas', 120, 'gram', 91, 94, 49),
(192, 'Selai nanas', 150, 'gram', 91, 94, 50),
(193, 'Telur', 3, 'buah', 91, 88, 49),
(194, 'Telur', 4, 'buah', 91, 88, 50),
(195, 'Tepung', 100, 'gram', 91, 85, 49),
(196, 'Tepung', 150, 'gram', 91, 85, 50),
(197, 'Keju', 40, 'gram', 91, 95, 49),
(198, 'Keju', 60, 'gram', 91, 95, 50),
(199, 'Kentang', 100, 'gram', 98, 96, NULL),
(200, 'Telur', 3, 'buah', 98, 88, NULL),
(201, 'Minyak goreng', 100, 'ml', 98, 76, NULL),
(202, 'Tepung', 300, 'gram', 94, 85, 51),
(203, 'Tepung', 500, 'gram', 94, 85, 52),
(204, 'Gula halus', 150, 'gram', 94, 97, 51),
(205, 'Gula halus', 180, 'gram', 94, 97, 52),
(206, 'Telur', 3, 'buah', 94, 88, 51),
(207, 'Telur', 4, 'buah', 94, 88, 52),
(208, 'Cabai ijo', 5, 'buah', NULL, 98, NULL),
(209, 'Seledri', 1, 'buah', NULL, 99, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_pelanggan` int NOT NULL,
  `nama_pelanggan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `profil_foto` varchar(255) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_pelanggan`, `nama_pelanggan`, `no_hp`, `alamat`, `profil_foto`, `latitude`, `longitude`) VALUES
(13, 'Bu C', '08369852147', 'Jl. Ring Road Utara, Condongcatur, Yogyakarta', '6af079c9370b4.jpg', -7.75947920, 110.39167440),
(18, 'Bu B', '08123459874', 'Bulaksumur, Yogyakarta', '6af079a3c64e8.png', -7.76984990, 110.38603310),
(19, 'Pak K', '08369852147', 'Karangmalang, Yogyakarta', '6af079d83e646.jpg', -7.77163120, 110.38496020);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan_bahan`
--

CREATE TABLE `detail_pesanan_bahan` (
  `id_detail` int NOT NULL,
  `jumlah_dipakai` double NOT NULL,
  `packing` varchar(255) NOT NULL,
  `fk_stok_detail` int DEFAULT NULL,
  `fk_bahan_detail` int DEFAULT NULL,
  `fk_detail_pesanan` int DEFAULT NULL,
  `status_beli` tinyint(1) DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan_bahan`
--

INSERT INTO `detail_pesanan_bahan` (`id_detail`, `jumlah_dipakai`, `packing`, `fk_stok_detail`, `fk_bahan_detail`, `fk_detail_pesanan`, `status_beli`, `updated_at`) VALUES
(22405, 3000, 'Mika', 73, 180, 651296, 0, NULL),
(22406, 60, 'Mika', 92, 181, 651296, 0, NULL),
(22407, 30, 'Mika', 93, 182, 651296, 0, NULL),
(22408, 30, 'Mika', 88, 183, 651296, 0, NULL),
(22409, 30, 'Mika', 83, 184, 651296, 0, NULL),
(22410, 30, 'Mika', 83, 185, 651296, 0, NULL),
(22411, 30, 'Mika', 99, 209, 651296, 0, NULL),
(22412, 3750, 'Box', 72, 152, 651297, 0, NULL),
(22413, 4000, 'Box', 73, 153, 651297, 0, NULL),
(22414, 200, 'Box', 74, 154, 651297, 0, NULL),
(22415, 100, 'Box', 75, 155, 651297, 0, NULL),
(22416, 500, 'Box', 76, 156, 651297, 0, NULL),
(22417, 5000, 'Box', 77, 157, 651297, 0, NULL),
(22418, 25, 'Box', 93, 186, 651298, 0, NULL),
(22419, 2500, 'Box', 85, 187, 651298, 0, NULL),
(22420, 3000, 'Box', 96, 199, 651299, 0, NULL),
(22421, 90, 'Box', 88, 200, 651299, 0, NULL),
(22422, 3000, 'Box', 76, 201, 651299, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `nama_menu` varchar(255) NOT NULL,
  `harga_menu` bigint NOT NULL,
  `gambar_menu` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga_menu`, `gambar_menu`, `kategori`) VALUES
(88, 'Nasi Cumi Sambel Ijo', 38000, '6af06c13741a6.jpg', 'Nasi Box'),
(89, 'Nasi Ayam Kecap', 22000, '6af06cd14f065.jpg', 'Nasi Box'),
(90, 'Nasi Cah Kangkung', 10000, '6af06d0bf01a9.jpg', 'Nasi Box'),
(91, 'Nastar', 35000, '6af06e6874732.png', 'Kue Kering'),
(92, 'Nasi Mendoan', 1000, '6af06e8f3cd60.jpg', 'Nasi Box'),
(93, 'Bolu Pisang', 28000, '6af06ef4506ba.png', 'Cake'),
(94, 'Putri Salju', 32000, '6af0700d98365.jpg', 'Kue Kering'),
(95, 'Nasi Kuning', 10000, '6af0703dcd115.png', 'Nasi Box'),
(96, 'Nasi Telur Balado', 20000, '6af070920bc33.jpg', 'Nasi Box'),
(97, 'Bakwan Sayur', 1000, '6af0718f00936.jpg', 'Nasi Box'),
(98, 'Perkedel Kentang', 1500, '6af071b175b9f.jpg', 'Nasi Box');

-- --------------------------------------------------------

--
-- Table structure for table `menu_varian`
--

CREATE TABLE `menu_varian` (
  `fk_menu_varian` int DEFAULT NULL,
  `id_varian` int NOT NULL,
  `harga_varian` int NOT NULL DEFAULT '0',
  `takaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu_varian`
--

INSERT INTO `menu_varian` (`fk_menu_varian`, `id_varian`, `harga_varian`, `takaran`) VALUES
(89, 47, 2000, 'Paha'),
(89, 48, 3000, 'Dada'),
(91, 49, 3000, '500 gram'),
(91, 50, 5000, '800 gram'),
(94, 51, 5000, '500 gram'),
(94, 52, 8000, '800 gram');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_total` int NOT NULL,
  `status_pemesanan` varchar(255) NOT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `catatan_khusus_pemesanan` text NOT NULL,
  `metode_pengantaran` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tanggal_antar` datetime NOT NULL,
  `fk_pesanan_varian` int DEFAULT NULL,
  `fk_pesanan_customer` int NOT NULL,
  `fk_menu` int DEFAULT NULL,
  `status_pengantaran` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `jumlah`, `harga_total`, `status_pemesanan`, `tanggal_pesan`, `catatan_khusus_pemesanan`, `metode_pengantaran`, `tanggal_antar`, `fk_pesanan_varian`, `fk_pesanan_customer`, `fk_menu`, `status_pengantaran`) VALUES
(651296, 30, 300000, 'Diproses', '2026-11-08 15:38:45', '-', 'Ojek Online', '2026-11-26 22:38:00', NULL, 18, 95, NULL),
(651297, 50, 1900000, 'Diantar', '2026-11-08 16:09:11', '-', 'Kurir Catering', '2026-11-19 23:09:00', NULL, 18, 88, 'Diterima'),
(651298, 25, 25000, 'Diterima', '2026-11-08 16:09:36', '-', 'Kurir Catering', '2026-11-20 23:09:00', NULL, 19, 92, NULL),
(651299, 30, 45000, 'Diterima', '2026-11-08 16:10:02', '', 'Kurir Catering', '2026-11-23 23:09:00', NULL, 13, 98, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stok_bahan`
--

CREATE TABLE `stok_bahan` (
  `id_stok` int NOT NULL,
  `nama_bahan_stok` varchar(255) NOT NULL,
  `stok_tersedia` double NOT NULL,
  `satuan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_bahan`
--

INSERT INTO `stok_bahan` (`id_stok`, `nama_bahan_stok`, `stok_tersedia`, `satuan`) VALUES
(72, 'Cumi-cumi', 0, 'gram'),
(73, 'Beras', 4000, 'gram'),
(74, 'Sambel ijo', 0, 'buah'),
(75, 'Garam', 0, 'sendok teh'),
(76, 'Minyak goreng', 0, 'ml'),
(77, 'Air', 5000, 'ml'),
(78, 'Kangkung', 0, 'lembar'),
(79, 'Cabai merah', 0, 'buah'),
(80, 'Saus tiram', 0, 'bungkus'),
(81, 'Bawang merah', 0, 'buah'),
(82, 'Kecap manis', 0, 'sendok makan'),
(83, 'Wortel', 0, 'buah'),
(84, 'Kubis', 0, 'buah'),
(85, 'Tepung', 0, 'gram'),
(86, 'Cabai', 0, 'buah'),
(87, 'Pisang', 0, 'buah'),
(88, 'Telur', 0, 'buah'),
(89, 'Gula', 0, 'gram'),
(90, 'Vanili', 0, 'buah'),
(91, 'Ayam', 0, 'gram'),
(92, 'Kunyit', 0, 'buah'),
(93, 'Tempe', 0, 'buah'),
(94, 'Selai nanas', 0, 'gram'),
(95, 'Keju', 0, 'gram'),
(96, 'Kentang', 3, 'kg'),
(97, 'Gula halus', 0, 'gram'),
(98, 'Cabai ijo', 0, 'buah'),
(99, 'Seledri', 30, 'buah'),
(100, 'Gula pasir', 2, 'kg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_depan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nama_belakang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pelanggan',
  `email` varchar(255) NOT NULL,
  `foto_profil_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `nama_depan`, `nama_belakang`, `password`, `role`, `email`, `foto_profil_user`) VALUES
(2, 'admin', 'admin', 'admin', '$2y$10$p0sKxxFcYteJ2QYaP32yS.arKMqCcnaWzO.yuQAWQHoTDPiM2Xf12', 'admin', 'admin12@gmail.com', '6a0f2102c9df7.png'),
(4, 'tim pengantaran', 'tim1', 'pengantaran1', '$2y$10$82MydHF5oLf9YIAMrKvPEuSM.L0fYl0atEVdlvBJqz9T712Wus4KS', 'tim pengantaran', 'pengantaran1@gmail.com', '6af0a688c18d3.jpg'),
(8, 'tim dapur', 'tim', 'Dapur', '$2y$10$tPWLvcNnOoMI/2dXpA9YuuixwLYsjkKX7CftJjEzO2RmyRgFYKjKi', 'tim dapur', 'dapur@gmail.com', '6af0a3f2255b1.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id_bahan`),
  ADD KEY `fk_bahan_menu` (`fk_menu_bahan`),
  ADD KEY `fk_bahan_stok` (`fk_bahan_stok`),
  ADD KEY `fk_bahan_varian` (`fk_varian_bahan`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `nama_pelanggan` (`nama_pelanggan`);

--
-- Indexes for table `detail_pesanan_bahan`
--
ALTER TABLE `detail_pesanan_bahan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_stok` (`fk_stok_detail`),
  ADD KEY `fk_detail_bahan` (`fk_bahan_detail`),
  ADD KEY `fk_dp_pesanan` (`fk_detail_pesanan`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `menu_varian`
--
ALTER TABLE `menu_varian`
  ADD PRIMARY KEY (`id_varian`),
  ADD KEY `fk_varian_menu` (`fk_menu_varian`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `fk_pesanan_varian` (`fk_pesanan_varian`),
  ADD KEY `fk_pesanan_customer` (`fk_pesanan_customer`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD PRIMARY KEY (`id_stok`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id_bahan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `detail_pesanan_bahan`
--
ALTER TABLE `detail_pesanan_bahan`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22423;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `menu_varian`
--
ALTER TABLE `menu_varian`
  MODIFY `id_varian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651300;

--
-- AUTO_INCREMENT for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  MODIFY `id_stok` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD CONSTRAINT `fk_bahan_menu` FOREIGN KEY (`fk_menu_bahan`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bahan_stok` FOREIGN KEY (`fk_bahan_stok`) REFERENCES `stok_bahan` (`id_stok`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bahan_varian` FOREIGN KEY (`fk_varian_bahan`) REFERENCES `menu_varian` (`id_varian`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `detail_pesanan_bahan`
--
ALTER TABLE `detail_pesanan_bahan`
  ADD CONSTRAINT `fk_detail_bahan` FOREIGN KEY (`fk_bahan_detail`) REFERENCES `bahan_baku` (`id_bahan`),
  ADD CONSTRAINT `fk_detail_stok` FOREIGN KEY (`fk_stok_detail`) REFERENCES `stok_bahan` (`id_stok`),
  ADD CONSTRAINT `fk_dp_pesanan` FOREIGN KEY (`fk_detail_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_varian`
--
ALTER TABLE `menu_varian`
  ADD CONSTRAINT `fk_varian_menu` FOREIGN KEY (`fk_menu_varian`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_customer` FOREIGN KEY (`fk_pesanan_customer`) REFERENCES `customer` (`id_pelanggan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_pesanan_varian` FOREIGN KEY (`fk_pesanan_varian`) REFERENCES `menu_varian` (`id_varian`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
