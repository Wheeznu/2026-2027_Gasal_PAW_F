# Modul 8 - Session dan Security

**Nama:** Wisnu Aji Saka Pratama Putra  
**NIM:** 25-099  
**Mata Kuliah:** Praktikum Pengembangan Aplikasi Web F

Website sederhana "Sistem Penjualan" dengan login, session, hak akses, dan logout.

## Fitur

- Login untuk level 1 (Owner) dan level 2 (Kasir).
- Session digunakan untuk menyimpan data user yang sedang login.
- Halaman selain login tidak bisa dibuka jika belum login.
- Halaman login tidak bisa dibuka jika sudah login.
- Menu Data Master hanya muncul dan hanya bisa diakses oleh Owner.
- CRUD Data Barang, Data Supplier, Data Pelanggan, dan Data User.
- CRUD Transaksi beserta detail barang.
- Laporan penjualan dengan filter tanggal, grafik, cetak, dan export Excel.
- Logout akan menghapus session dan kembali ke halaman login.

## Akun Demo

| Level | Username | Password | Nama |
|-------|----------|----------|------|
| 1 - Owner | `admin` | `admin123` | Administrator Utama |
| 2 - Kasir | `kasir1` | `kasir123` | Budi Santoso |
| 2 - Kasir | `kasir2` | `kasir123` | Siti Nurhaliza |

## Cara Menjalankan

1. Jalankan Apache dan MySQL (XAMPP).
2. Import file `database.sql` ke MySQL.
   - Lewat phpMyAdmin: pilih menu Import, lalu pilih file `database.sql`.
   - Lewat terminal:
     ```bash
     /opt/lampp/bin/mysql -u root < database.sql
     ```
3. Letakkan folder ini di dalam `htdocs`, lalu buka:
   ```
   http://localhost/Modul-8/25-099_Wisnu_Aji_Saka_Pratama_Putra/auth/login.php
   ```
   Bisa juga menjalankan PHP built-in server dari folder ini:
   ```bash
   php -S localhost:8000
   ```
   lalu buka `http://localhost:8000/auth/login.php`.
4. Login menggunakan akun demo di atas. Setelah login, Anda akan diarahkan ke halaman Home (`layout/index.php`).

## Struktur File

```
.
├── auth/
│   ├── login.php       # Halaman dan proses login
│   ├── logout.php      # Proses logout
│   ├── cek_login.php   # Pengecekan session untuk semua halaman
│   └── cek_owner.php   # Pengecekan level 1 untuk Data Master
├── crud/
│   ├── barang.php      # CRUD data barang
│   ├── supplier.php    # CRUD data supplier
│   ├── pelanggan.php   # CRUD data pelanggan
│   └── user.php        # CRUD data user
├── transaksi/
│   ├── transaksi.php   # CRUD transaksi
│   └── detail.php      # Detail item transaksi
├── laporan/
│   ├── laporan.php     # Laporan penjualan
│   └── export_excel.php # Export laporan ke Excel
├── layout/
│   ├── header.php      # Navbar dan pembuka HTML
│   ├── footer.php      # Penutup HTML
│   ├── index.php       # Home
│   └── koneksi.php     # Koneksi database
├── style.css           # CSS sederhana untuk merapikan tampilan
├── database.sql        # Struktur dan data awal database
└── README.md
```

## Catatan

- Password disimpan menggunakan `md5` karena mengikuti materi praktikum. Untuk aplikasi produksi sebaiknya menggunakan `password_hash()` dan `password_verify()`.
- Nama database yang dipakai adalah `db_penjualan_8`. Jika ingin mengganti nama database, ubah bagian `mysqli_connect()` di `koneksi.php`.
- Jika MySQL menggunakan password, sesuaikan juga parameter password pada `koneksi.php`.
