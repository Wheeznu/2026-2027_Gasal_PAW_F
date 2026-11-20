-- Database untuk Modul 8: Session dan Security
-- Nama database: db_penjualan_8

CREATE DATABASE IF NOT EXISTS `db_penjualan_8`;
USE `db_penjualan_8`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `transaksi_detail`;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `barang`;
DROP TABLE IF EXISTS `pelanggan`;
DROP TABLE IF EXISTS `supplier`;
DROP TABLE IF EXISTS `user`;

SET FOREIGN_KEY_CHECKS = 1;

-- ===== Tabel supplier =====
CREATE TABLE `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`id`, `nama`, `telp`, `alamat`) VALUES
(1, 'PT Sumber Makmur', '081234567890', 'Jl. Industri No. 10, Surabaya'),
(2, 'CV Jaya Abadi', '082345678901', 'Jl. Raya Malang No. 5, Malang'),
(3, 'UD Berkah Sentosa', '083456789012', 'Jl. Pahlawan No. 22, Sidoarjo');

-- ===== Tabel pelanggan =====
CREATE TABLE `pelanggan` (
  `id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pelanggan` (`id`, `nama`, `jenis_kelamin`, `telp`, `alamat`) VALUES
('PLG001', 'Andi Wijaya', 'L', '085100000001', 'Jl. Merdeka No. 1, Surabaya'),
('PLG002', 'Siti Nurhaliza', 'P', '085200000002', 'Jl. Kenanga No. 12, Malang'),
('PLG003', 'Budi Santoso', 'L', '085300000003', 'Jl. Mawar No. 8, Sidoarjo'),
('PLG004', 'Dewi Lestari', 'P', '085400000004', 'Jl. Melati No. 3, Gresik'),
('PLG005', 'Rizky Pratama', 'L', '085500000005', 'Jl. Dahlia No. 15, Surabaya');

-- ===== Tabel user =====
-- Password disimpan dengan md5 sesuai materi praktikum.
-- admin  : admin123
-- kasir1 : kasir123
-- kasir2 : kasir123
CREATE TABLE `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(35) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `hp` varchar(20) DEFAULT NULL,
  `level` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `alamat`, `hp`, `level`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator Utama', 'Jl. Pemuda No. 1, Surabaya', '081234567890', 1),
(2, 'kasir1', 'de28f8f7998f23ab4194b51a6029416f', 'Budi Santoso', 'Jl. Basuki Rahmat No. 12, Surabaya', '081234567891', 2),
(3, 'kasir2', 'de28f8f7998f23ab4194b51a6029416f', 'Siti Nurhaliza', 'Jl. Diponegoro No. 45, Surabaya', '081234567892', 2);

-- ===== Tabel barang =====
CREATE TABLE `barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `supplier_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `fk_barang_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `harga`, `stok`, `supplier_id`) VALUES
(1, 'BRG001', 'Beras Premium 5kg', 75000, 100, 1),
(2, 'BRG002', 'Minyak Goreng 2L', 35000, 150, 1),
(3, 'BRG003', 'Gula Pasir 1kg', 15000, 200, 2),
(4, 'BRG004', 'Kopi Bubuk 250g', 25000, 80, 2),
(5, 'BRG005', 'Teh Celup 25pcs', 12000, 120, 3),
(6, 'BRG006', 'Susu UHT 1L', 18000, 90, 3);

-- ===== Tabel transaksi =====
CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `waktu_transaksi` date NOT NULL,
  `keterangan` text,
  `total` int NOT NULL DEFAULT 0,
  `pelanggan_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pelanggan_id` (`pelanggan_id`),
  CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi` (`id`, `waktu_transaksi`, `keterangan`, `total`, `pelanggan_id`) VALUES
(1, '2026-11-01', 'Pembelian bahan pokok', 185000, 'PLG001'),
(2, '2026-11-01', 'Belanja bulanan', 85000, 'PLG002'),
(3, '2026-11-02', 'Pembelian kopi dan teh', 52000, 'PLG003'),
(4, '2026-11-03', 'Belanja mingguan', 145000, 'PLG001'),
(5, '2026-11-03', 'Pembelian gula dan minyak', 65000, 'PLG004'),
(6, '2026-11-03', 'Belanja harian', 42000, 'PLG005'),
(7, '2026-11-04', 'Pembelian susu', 54000, 'PLG002'),
(8, '2026-11-05', 'Belanja besar', 220000, 'PLG003'),
(9, '2026-11-05', 'Pembelian beras', 150000, 'PLG005'),
(10, '2026-11-06', 'Belanja camilan', 37000, 'PLG004'),
(11, '2026-11-07', 'Pembelian bulanan', 293000, 'PLG001'),
(12, '2026-11-07', 'Belanja dapur', 67000, 'PLG002'),
(13, '2026-11-08', 'Pembelian minyak', 70000, 'PLG003'),
(14, '2026-11-09', 'Belanja pagi', 45000, 'PLG005'),
(15, '2026-11-10', 'Pembelian lengkap', 245000, 'PLG001'),
(16, '2026-11-10', 'Belanja teh dan kopi', 37000, 'PLG004'),
(17, '2026-11-11', 'Pembelian beras dan gula', 90000, 'PLG002'),
(18, '2026-11-12', 'Belanja harian', 55000, 'PLG003'),
(19, '2026-11-13', 'Pembelian susu dan teh', 30000, 'PLG005'),
(20, '2026-11-14', 'Belanja mingguan', 150000, 'PLG001');

-- ===== Tabel transaksi_detail =====
CREATE TABLE `transaksi_detail` (
  `transaksi_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `harga` int NOT NULL,
  `qty` int NOT NULL,
  PRIMARY KEY (`transaksi_id`, `barang_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_detail` (`transaksi_id`, `barang_id`, `harga`, `qty`) VALUES
(1, 1, 75000, 2), (1, 2, 35000, 1),
(2, 3, 15000, 2), (2, 4, 25000, 1), (2, 5, 12000, 1), (2, 6, 18000, 1),
(3, 4, 25000, 1), (3, 5, 12000, 1), (3, 3, 15000, 1),
(4, 1, 75000, 1), (4, 2, 35000, 2),
(5, 3, 15000, 2), (5, 2, 35000, 1),
(6, 5, 12000, 2), (6, 6, 18000, 1),
(7, 6, 18000, 3),
(8, 1, 75000, 2), (8, 2, 35000, 2),
(9, 1, 75000, 2),
(10, 4, 25000, 1), (10, 5, 12000, 1),
(11, 1, 75000, 2), (11, 2, 35000, 2), (11, 3, 15000, 2), (11, 4, 25000, 1), (11, 6, 18000, 1),
(12, 4, 25000, 1), (12, 5, 12000, 1), (12, 3, 15000, 2),
(13, 2, 35000, 2),
(14, 5, 12000, 1), (14, 3, 15000, 1), (14, 6, 18000, 1),
(15, 1, 75000, 2), (15, 2, 35000, 2), (15, 4, 25000, 1),
(16, 4, 25000, 1), (16, 5, 12000, 1),
(17, 1, 75000, 1), (17, 3, 15000, 1),
(18, 4, 25000, 1), (18, 6, 18000, 1), (18, 5, 12000, 1),
(19, 6, 18000, 1), (19, 5, 12000, 1),
(20, 1, 75000, 1), (20, 2, 35000, 1), (20, 4, 25000, 1), (20, 3, 15000, 1);
