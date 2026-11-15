CREATE DATABASE IF NOT EXISTS `db_penjualan_7`;
USE `db_penjualan_7`;


-- Struktur dari tabel `supplier`

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `telp` varchar(12) NOT NULL,
  `alamat` text NOT NULL,
  PRIMARY KEY (`id`)
);


-- Struktur dari tabel `pelanggan`

CREATE TABLE `pelanggan` (
  `id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `telp` varchar(12) NOT NULL,
  `alamat` text NOT NULL,
  PRIMARY KEY (`id`)
);


-- Struktur dari tabel `user`

CREATE TABLE `user` (
  `id_user` tinyint(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(35) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(150) NOT NULL,
  `hp` varchar(20) NOT NULL,
  `level` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_user`)
);


-- Struktur dari tabel `barang`

CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Struktur dari tabel `transaksi`

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `waktu_transaksi` date NOT NULL,
  `keterangan` text NOT NULL,
  `total` int(11) NOT NULL,
  `pelanggan_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pelanggan_id` (`pelanggan_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Struktur dari tabel `transaksi_detail`

CREATE TABLE `transaksi_detail` (
  `transaksi_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`transaksi_id`, `barang_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Struktur dari tabel `pembayaran`

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `waktu_bayar` datetime NOT NULL,
  `total` int(11) NOT NULL,
  `metode` enum('TUNAI','TRANSFER','EDC') NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_id` (`transaksi_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Data seed


INSERT INTO `supplier` (`id`, `nama`, `telp`, `alamat`) VALUES
(1, 'PT Sumber Makmur', '081234567890', 'Jl. Industri No. 10, Surabaya'),
(2, 'CV Jaya Abadi', '082345678901', 'Jl. Raya Malang No. 5, Malang'),
(3, 'UD Berkah Sentosa', '083456789012', 'Jl. Pahlawan No. 22, Sidoarjo');

INSERT INTO `pelanggan` (`id`, `nama`, `jenis_kelamin`, `telp`, `alamat`) VALUES
('PLG001', 'Andi Wijaya', 'L', '085100000001', 'Jl. Merdeka No. 1, Surabaya'),
('PLG002', 'Siti Nurhaliza', 'P', '085200000002', 'Jl. Kenanga No. 12, Malang'),
('PLG003', 'Budi Santoso', 'L', '085300000003', 'Jl. Mawar No. 8, Sidoarjo'),
('PLG004', 'Dewi Lestari', 'P', '085400000004', 'Jl. Melati No. 3, Gresik'),
('PLG005', 'Rizky Pratama', 'L', '085500000005', 'Jl. Dahlia No. 15, Surabaya');

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `harga`, `stok`, `supplier_id`) VALUES
(1, 'BRG001', 'Beras Premium 5kg', 75000, 100, 1),
(2, 'BRG002', 'Minyak Goreng 2L', 35000, 150, 1),
(3, 'BRG003', 'Gula Pasir 1kg', 15000, 200, 2),
(4, 'BRG004', 'Kopi Bubuk 250g', 25000, 80, 2),
(5, 'BRG005', 'Teh Celup 25pcs', 12000, 120, 3),
(6, 'BRG006', 'Susu UHT 1L', 18000, 90, 3);

INSERT INTO `transaksi` (`id`, `waktu_transaksi`, `keterangan`, `total`, `pelanggan_id`) VALUES
(1,  '2026-11-01', 'Pembelian bahan pokok', 185000, 'PLG001'),
(2,  '2026-11-01', 'Belanja bulanan', 95000,  'PLG002'),
(3,  '2026-11-02', 'Pembelian kopi dan teh', 62000,  'PLG003'),
(4,  '2026-11-03', 'Belanja mingguan', 145000, 'PLG001'),
(5,  '2026-11-03', 'Pembelian gula dan minyak', 50000,  'PLG004'),
(6,  '2026-11-03', 'Belanja harian', 36000,  'PLG005'),
(7,  '2026-11-04', 'Pembelian susu', 54000,  'PLG002'),
(8,  '2026-11-05', 'Belanja besar', 220000, 'PLG003'),
(9,  '2026-11-05', 'Pembelian beras', 150000, 'PLG005'),
(10, '2026-11-06', 'Belanja camilan', 37000,  'PLG004'),
(11, '2026-11-07', 'Pembelian bulanan', 310000, 'PLG001'),
(12, '2026-11-07', 'Belanja dapur', 68000,  'PLG002'),
(13, '2026-11-08', 'Pembelian minyak', 70000,  'PLG003'),
(14, '2026-11-09', 'Belanja pagi', 43000,  'PLG005'),
(15, '2026-11-10', 'Pembelian lengkap', 275000, 'PLG001'),
(16, '2026-11-10', 'Belanja teh dan kopi', 37000,  'PLG004'),
(17, '2026-11-11', 'Pembelian beras dan gula', 90000,  'PLG002'),
(18, '2026-11-12', 'Belanja harian', 53000,  'PLG003'),
(19, '2026-11-13', 'Pembelian susu dan teh', 30000,  'PLG005'),
(20, '2026-11-14', 'Belanja mingguan', 160000, 'PLG001');

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

INSERT INTO `pembayaran` (`id`, `waktu_bayar`, `total`, `metode`, `transaksi_id`) VALUES
(1,  '2026-11-01 08:30:00', 185000, 'TUNAI', 1),
(2,  '2026-11-01 10:15:00', 95000,  'TRANSFER', 2),
(3,  '2026-11-02 09:00:00', 62000,  'TUNAI', 3),
(4,  '2026-11-03 11:20:00', 145000, 'EDC', 4),
(5,  '2026-11-03 13:45:00', 50000,  'TUNAI', 5),
(6,  '2026-11-03 15:00:00', 36000,  'TRANSFER', 6),
(7,  '2026-11-04 08:10:00', 54000,  'TUNAI', 7),
(8,  '2026-11-05 10:30:00', 220000, 'EDC', 8),
(9,  '2026-11-05 14:00:00', 150000, 'TRANSFER', 9),
(10, '2026-11-06 09:45:00', 37000,  'TUNAI', 10),
(11, '2026-11-07 08:00:00', 310000, 'EDC', 11),
(12, '2026-11-07 12:30:00', 68000,  'TUNAI', 12),
(13, '2026-11-08 10:00:00', 70000,  'TRANSFER', 13),
(14, '2026-11-09 07:30:00', 43000,  'TUNAI', 14),
(15, '2026-11-10 09:15:00', 275000, 'EDC', 15),
(16, '2026-11-10 11:00:00', 37000,  'TUNAI', 16),
(17, '2026-11-11 08:45:00', 90000,  'TRANSFER', 17),
(18, '2026-11-12 10:20:00', 53000,  'TUNAI', 18),
(19, '2026-11-13 09:00:00', 30000,  'TUNAI', 19),
(20, '2026-11-14 11:30:00', 160000, 'EDC', 20);