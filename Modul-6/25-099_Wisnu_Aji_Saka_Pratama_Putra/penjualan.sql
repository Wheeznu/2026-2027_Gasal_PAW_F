SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`nama`, `telp`, `alamat`) VALUES
('PT. Maju Bersama', '08123456789', 'Surabaya'),
('PT. Senang Sekali', '08151556300', 'Bangkalan'),
('PT. Segar Segar', '08454546630', 'Surabaya'),
('CV. Sumber Makmur', '081298765431', 'Sidoarjo'),
('PT. Berkah Sentosa', '081298765432', 'Gresik');

CREATE TABLE `barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `supplier_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_barang_supplier` (`supplier_id`),
  CONSTRAINT `fk_barang_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barang` (`kode_barang`, `nama_barang`, `harga`, `stok`, `supplier_id`) VALUES
('BRG001', 'Buku Tulis Sinar Dunia 38 Lembar', 3500, 120, 1),
('BRG002', 'Pulpen Standard AE7 0.5 Black', 2500, 250, 1),
('BRG003', 'Penghapus Joyko Hitam', 2000, 80, 2),
('BRG004', 'Penggaris Besi 30 cm', 6000, 50, 2),
('BRG005', 'Kertas HVS A4 Natural 75gr', 45000, 40, 3),
('BRG006', 'Spidol Boardmarker Snowman Hitam', 8500, 90, 4),
('BRG007', 'Tipe-X Kertas Joyko Roller', 7000, 65, 5),
('BRG008', 'Map Dokumen Clear Holder A4 20P', 15000, 30, 4),
('BRG009', 'Stapler Joyko HD-10', 12000, 45, 3),
('BRG010', 'Gunting Sedang Joyko', 9500, 35, 2);

CREATE TABLE `pelanggan` (
  `id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pelanggan` (`id`, `nama`, `jenis_kelamin`, `telp`, `alamat`) VALUES
('PLG001', 'Ahmad Dahlan', 'L', '082111222331', 'Jl. Kenjeran No. 10, Surabaya'),
('PLG002', 'Bambang Sudarsono', 'L', '082111222332', 'Jl. Manukan Kulon No. 4, Surabaya'),
('PLG003', 'Citra Kirana', 'P', '082111222333', 'Jl. Manyar Kertoarjo No. 18, Surabaya'),
('PLG004', 'Dina Mariana', 'P', '082111222334', 'Jl. Dharmahusada No. 25, Surabaya'),
('PLG005', 'Erik Setiawan', 'L', '082111222335', 'Jl. Kutai No. 8, Surabaya');

CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `waktu_transaksi` date NOT NULL,
  `keterangan` text,
  `total` int NOT NULL DEFAULT 0,
  `pelanggan_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transaksi_pelanggan` (`pelanggan_id`),
  CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi` (`waktu_transaksi`, `keterangan`, `total`, `pelanggan_id`) VALUES
('2026-03-01', 'Belanja alat tulis kantor', 10500, 'PLG001'),
('2026-03-01', 'Pembelian keperluan perlengkapan', 45000, 'PLG002'),
('2026-03-02', 'Pesanan ATK sekolah', 17000, 'PLG003'),
('2026-03-02', 'Belanja rutin mingguan', 19000, 'PLG004'),
('2026-03-03', 'Pembelian spidol dan buku', 20500, 'PLG005'),
('2026-03-03', 'Perlengkapan arsip kantor', 30000, 'PLG001'),
('2026-03-04', 'Beli eceran', 5000, 'PLG002'),
('2026-03-04', 'Kebutuhan sekretariat', 21500, 'PLG003'),
('2026-03-05', 'Pembelian kertas tambahan', 90000, 'PLG004'),
('2026-03-05', 'Kebutuhan ujian siswa', 14000, 'PLG005');

CREATE TABLE `transaksi_detail` (
  `transaksi_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `harga` int NOT NULL,
  `qty` int NOT NULL,
  PRIMARY KEY (`transaksi_id`, `barang_id`),
  KEY `fk_detail_barang` (`barang_id`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_detail` (`transaksi_id`, `barang_id`, `harga`, `qty`) VALUES
(1, 1, 3500, 3),
(2, 5, 45000, 1),
(3, 6, 8500, 2),
(4, 7, 7000, 1),
(4, 9, 12000, 1),
(5, 1, 3500, 1),
(5, 6, 8500, 2),
(6, 8, 15000, 2),
(7, 2, 2500, 2),
(8, 9, 12000, 1),
(8, 10, 9500, 1),
(9, 5, 45000, 2),
(10, 7, 7000, 2);

COMMIT;
