-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2023 at 10:26 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gdg`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang_keluar`
--

CREATE TABLE `tb_barang_keluar` (
  `id` int(10) NOT NULL,
  `id_transaksi` varchar(50) NOT NULL,
  `tanggal_masuk` varchar(20) NOT NULL,
  `tanggal_keluar` varchar(20) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `kode_barang` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `jumlah` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_barang_keluar`
--

INSERT INTO `tb_barang_keluar` (`id`, `id_transaksi`, `tanggal_masuk`, `tanggal_keluar`, `lokasi`, `kode_barang`, `nama_barang`, `satuan`, `jumlah`) VALUES
(20, 'WG-202232965817', '30/12/2022', '03/01/2023', 'Jawa Barat', '001', 'asd', '001', '5'),
(21, 'WG-202310753682', '27/12/2022', '06/01/2023', 'Bengkulu', 'fr', 'garam', '001', '100'),
(22, 'WG-202374813526', '06/01/2023', '03/01/2023', 'Maluku', '231', 'wadin', '001', '4000'),
(23, 'WG-202374813526', '06/01/2023', '05/01/2023', 'Maluku', '231', 'wadin', '001', '99'),
(24, 'WG-202374813526', '06/01/2023', '05/01/2023', 'Maluku', '231', 'wadin', '001', '12'),
(25, 'WG-202393724518', '30/01/2023', '19/01/2023', 'Papua', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzz', '001', '20'),
(26, 'WG-202303284976', '17/01/2023', '18/01/2023', 'Jakarta', 'kkkkkkkkkkkkkkkkkkkkk', 'kkkkkkkkkkkkkkkkk', '001', '87'),
(27, 'WG-202310253867', '05/01/2023', '18/01/2023', 'Jakarta', '231', 'glue', '001', '12');

--
-- Triggers `tb_barang_keluar`
--
DELIMITER $$
CREATE TRIGGER `TG_BARANG_KELUAR` AFTER INSERT ON `tb_barang_keluar` FOR EACH ROW BEGIN
 UPDATE tb_barang_masuk SET jumlah=jumlah-NEW.jumlah
 WHERE kode_barang=NEW.kode_barang;
 DELETE FROM tb_barang_masuk WHERE jumlah = 0;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang_masuk`
--

CREATE TABLE `tb_barang_masuk` (
  `id_transaksi` varchar(50) NOT NULL,
  `tanggal` varchar(20) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `kode_barang` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `jumlah` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_barang_masuk`
--

INSERT INTO `tb_barang_masuk` (`id_transaksi`, `tanggal`, `lokasi`, `kode_barang`, `nama_barang`, `satuan`, `jumlah`) VALUES
('askndasndnsj', '18/01/2023', 'Bengkulu', 'sadasd', 'asdadas', '001', '10'),
('WG-202313059427', '16/01/2023', 'Riau', 'T300pQ', 'CO2', '', '20'),
('WG-202351784029', '17/01/2023', 'Sulawesi Selatan', 'QRcode', 'compound', '001', '20000000'),
('WG-202361245087', '18/01/2023', 'Bengkulu', '231', 'compound', '001', '-10'),
('WG-202391372806', '14/01/2023', 'Jawa Tengah', '32131', 'compound', '', '20');

-- --------------------------------------------------------

--
-- Table structure for table `tb_compound_keluar`
--

CREATE TABLE `tb_compound_keluar` (
  `id` int(10) NOT NULL,
  `idx` int(10) DEFAULT NULL,
  `supplier` varchar(50) NOT NULL,
  `material` varchar(50) NOT NULL,
  `pellet` varchar(20) NOT NULL,
  `partno` varchar(50) NOT NULL,
  `receivedate` date NOT NULL,
  `lotnumber` varchar(50) NOT NULL,
  `mfgdate` date NOT NULL,
  `expdate` date NOT NULL,
  `stock` int(10) NOT NULL,
  `jumlah` int(100) NOT NULL,
  `balance` int(10) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_compound_keluar`
--

INSERT INTO `tb_compound_keluar` (`id`, `idx`, `supplier`, `material`, `pellet`, `partno`, `receivedate`, `lotnumber`, `mfgdate`, `expdate`, `stock`, `jumlah`, `balance`, `remarks`) VALUES
(1, 1, 'sumikon', 'compound', '25x25', 'T9012321Q', '2023-01-10', 'R251213', '2023-01-11', '2023-01-14', 2, 200, 1, 'Material masih berada diwirehouse'),
(2, NULL, 'Sumikon', '', '24x21', 'dasa', '0000-00-00', '32224erwerew', '0000-00-00', '0000-00-00', 1, 300, 1, 'Masih berada diwirehouse');

-- --------------------------------------------------------

--
-- Table structure for table `tb_compound_masuk`
--

CREATE TABLE `tb_compound_masuk` (
  `idx` int(50) NOT NULL,
  `supplier` varchar(50) NOT NULL,
  `material` varchar(50) NOT NULL,
  `pellet` varchar(50) NOT NULL,
  `partno` varchar(50) NOT NULL,
  `receivedate` varchar(10) NOT NULL,
  `lotnumber` varchar(50) NOT NULL,
  `mfgdate` varchar(10) NOT NULL,
  `expdate` varchar(10) NOT NULL,
  `stock` int(10) NOT NULL,
  `jumlah` int(10) NOT NULL,
  `balance` int(10) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_compound_masuk`
--

INSERT INTO `tb_compound_masuk` (`idx`, `supplier`, `material`, `pellet`, `partno`, `receivedate`, `lotnumber`, `mfgdate`, `expdate`, `stock`, `jumlah`, `balance`, `remarks`) VALUES
(5, 'dhaihatsu', 'compound', 'z3r0', '9000rpm', '18/01/2023', 'z3r0', '20/01/2023', '24/01/2023', 3, 50000, 1, 'masih  diwirehouse'),
(7, 'sumikon', 'compound', '23x25', '2000rpm', '24/01/2023', '300tpQ1', '25/01/2023', '27/01/2023', 2, 200, 1, 'masih  diwirehouse');

-- --------------------------------------------------------

--
-- Table structure for table `tb_glue_masuk`
--

CREATE TABLE `tb_glue_masuk` (
  `idx` int(10) NOT NULL,
  `supplier` varchar(50) NOT NULL,
  `gluetype` varchar(50) NOT NULL,
  `partno` varchar(50) NOT NULL,
  `lotnumber` varchar(50) NOT NULL,
  `mfgdate` date NOT NULL,
  `expdate` date NOT NULL,
  `jumlah` int(10) NOT NULL,
  `balance` int(10) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_leadframe_keluar`
--

CREATE TABLE `tb_leadframe_keluar` (
  `id` int(10) NOT NULL,
  `leadframetype` varchar(50) NOT NULL,
  `partno` varchar(50) NOT NULL,
  `lotnumber` varchar(50) NOT NULL,
  `mfgdate` date NOT NULL,
  `expdate` date NOT NULL,
  `stock` varchar(25) NOT NULL,
  `used` int(10) NOT NULL,
  `balance` int(10) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_leadframe_masuk`
--

CREATE TABLE `tb_leadframe_masuk` (
  `id` int(10) NOT NULL,
  `supplier` varchar(50) NOT NULL,
  `leadframetype` varchar(50) NOT NULL,
  `partno` int(10) NOT NULL,
  `lotnumber` varchar(50) NOT NULL,
  `mfgdate` date NOT NULL,
  `expdate` date NOT NULL,
  `stock` int(50) NOT NULL,
  `jumlah` int(50) NOT NULL,
  `balance` int(50) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_satuan`
--

CREATE TABLE `tb_satuan` (
  `id_satuan` int(11) NOT NULL,
  `kode_satuan` varchar(100) NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_satuan`
--

INSERT INTO `tb_satuan` (`id_satuan`, `kode_satuan`, `nama_satuan`) VALUES
(6, '001', 'Ton');

-- --------------------------------------------------------

--
-- Table structure for table `tb_upload_gambar_user`
--

CREATE TABLE `tb_upload_gambar_user` (
  `id` int(11) NOT NULL,
  `username_user` varchar(100) NOT NULL,
  `nama_file` varchar(220) NOT NULL,
  `ukuran_file` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_upload_gambar_user`
--

INSERT INTO `tb_upload_gambar_user` (`id`, `username_user`, `nama_file`, `ukuran_file`) VALUES
(2, 'admin', 'YtFlash1.png', '44.45'),
(4, 'bobi', 'YtFlash1.png', '44.45'),
(5, 'ika', 'nopic2.png', '6.33');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(12) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `last_login` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`, `last_login`) VALUES
(20, 'admin', 'admin@gmail.com', '$2y$10$efgqMhLxK9JIQmTczpCk6O8eAeRAAy1OYwUvvQv/sXLOU/LH8C7je', 1, '24-01-2023 7:22'),
(22, 'bobi', 'admin.bobi@gmail.com', '$2y$10$LPzw8B.3FdUajDrYEFdBoOc04TTOqIkVFVkH6c1gKz78spVLw5H5.', 1, '10-01-2023 9:14'),
(24, 'test', 'asdasd@asdas.com', '$2y$10$HpsbQk9Ix3ADJaDAPHbW1.2aWWlyBAHy3rOMIZHP3ozBnHMjOxXqy', 0, '30-12-2022 14:52'),
(25, 'marco', 'marcokop@gmail.com', '$2y$10$nhCbwGrvorXJVeEtrFJQzuhv1Cb4q0MULOeWafpmngl/crY.qmQCa', 1, '17-01-2023 3:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_barang_keluar`
--
ALTER TABLE `tb_barang_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_barang_masuk`
--
ALTER TABLE `tb_barang_masuk`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `tb_compound_keluar`
--
ALTER TABLE `tb_compound_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_compound_masuk`
--
ALTER TABLE `tb_compound_masuk`
  ADD PRIMARY KEY (`idx`);

--
-- Indexes for table `tb_glue_masuk`
--
ALTER TABLE `tb_glue_masuk`
  ADD PRIMARY KEY (`idx`);

--
-- Indexes for table `tb_leadframe_keluar`
--
ALTER TABLE `tb_leadframe_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_leadframe_masuk`
--
ALTER TABLE `tb_leadframe_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_satuan`
--
ALTER TABLE `tb_satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `tb_upload_gambar_user`
--
ALTER TABLE `tb_upload_gambar_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_barang_keluar`
--
ALTER TABLE `tb_barang_keluar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tb_compound_keluar`
--
ALTER TABLE `tb_compound_keluar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_compound_masuk`
--
ALTER TABLE `tb_compound_masuk`
  MODIFY `idx` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_glue_masuk`
--
ALTER TABLE `tb_glue_masuk`
  MODIFY `idx` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_leadframe_keluar`
--
ALTER TABLE `tb_leadframe_keluar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_leadframe_masuk`
--
ALTER TABLE `tb_leadframe_masuk`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_satuan`
--
ALTER TABLE `tb_satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_upload_gambar_user`
--
ALTER TABLE `tb_upload_gambar_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
