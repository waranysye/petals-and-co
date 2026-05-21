# 🌸 Petals & Co. — Florist Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker Compose](https://img.shields.io/badge/Docker_Compose-Supported-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![Nginx](https://img.shields.io/badge/Nginx-Web_Server-009639?style=for-the-badge&logo=nginx&logoColor=white)](https://nginx.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**Petals & Co.** adalah sistem informasi penjualan dan manajemen toko bunga (florist) berbasis web yang dirancang khusus untuk mempermudah operasional toko sekaligus memberikan pengalaman berbelanja yang menyenangkan bagi pelanggan. 

Aplikasi ini memiliki arsitektur **PHP Native** berkinerja tinggi, terintegrasi penuh dengan basis data **MySQL**, dan telah dikemas menggunakan **Docker & Docker Compose** untuk kemudahan deployment.

---

## 🚀 Fitur Utama

Sistem ini terbagi menjadi dua portal utama yang responsif dan interaktif:

### 👤 Portal Pelanggan (Customer)
*   **Katalog Interaktif:** Menampilkan koleksi bunga cantik berdasarkan kategori (Mawar, Tulip, Anggrek, dll.) dengan desain modern.
*   **Keranjang Belanja (Shopping Cart):** Menambah, mengurangi jumlah, dan menghapus pesanan secara dinamis.
*   **Checkout & Pembayaran Mandiri:** Mendukung metode pembayaran Transfer Bank & Cash on Delivery (COD) disertai fitur unggah bukti transfer.
*   **Riwayat Transaksi:** Melacak status pemesanan secara *real-time* (`Pending`, `Processing`, `Shipped`, `Delivered`, `Cancelled`).
*   **Manajemen Profil:** Mengelola informasi kontak, telepon, dan alamat pengiriman dengan mudah.

### 🔑 Portal Administrator (Admin)
*   **Dashboard Utama:** Tampilan ringkas mengenai statistik penjualan dan ringkasan pesanan masuk.
*   **Manajemen Produk (CRUD):** Menambah, mengubah, dan menghapus katalog bunga beserta gambar, deskripsi, harga, dan manajemen stok otomatis.
*   **Manajemen Pelanggan:** Melihat dan memantau daftar pelanggan yang terdaftar pada sistem.
*   **Manajemen Pesanan:** Memvalidasi bukti pembayaran pelanggan dan memperbarui status pengiriman bunga.
*   **Laporan Penjualan (Reports):** Analisis transaksi harian/bulanan untuk memantau performa toko bunga.

---

## 🛠️ Teknologi yang Digunakan

*   **Backend:** PHP 8.2 (Native, MySQLi, PDO)
*   **Database:** MySQL 8.0
*   **Frontend:** HTML5, Vanilla CSS3 (Custom-designed layouts), JavaScript
*   **Web Server:** Nginx (Reverse Proxy & Static File Serving)
*   **Virtualisasi:** Docker & Docker Compose
*   **Database Tool:** phpMyAdmin (terintegrasi dalam environment Docker)

---

## 📂 Struktur Folder Proyek

```text
Florist/
├── admin/                  # Halaman dan fungsionalitas panel Admin
│   ├── add_product.php     # Tambah produk bunga
│   ├── customers.php       # Kelola pelanggan
│   ├── edit_product.php    # Ubah data produk
│   ├── index.php           # Dashboard admin
│   ├── products.php        # Daftar produk
│   ├── reports.php         # Laporan penjualan
│   └── ...
├── auth/                   # Autentikasi sistem (Login & Registrasi)
│   ├── login.php
│   └── register.php
├── costumer/               # Portal utama Pelanggan (Customer)
│   ├── collections.php     # Katalog bunga
│   ├── cart.php            # Keranjang belanja
│   ├── idexcostumer.php    # Halaman beranda customer
│   ├── payment.php         # Form pembayaran & unggah bukti
│   ├── transaction.php     # Riwayat transaksi
│   └── ...
├── Config/                 # Konfigurasi aplikasi
│   └── database.php        # Konfigurasi koneksi MySQL database
├── Assets/                 # File static (Aset desain)
│   ├── css/                # Custom CSS per halaman
│   ├── img/                # Gambar produk, logo, dan ilustrasi
│   └── js/                 # File JavaScript interaktif
├── includes/               # Komponen template (Header, Footer, Sidebar)
├── database.sql            # Skema dan data awal database MySQL
├── docker-compose.yml      # Orkestrasi container Docker
├── Dockerfile              # Konfigurasi container PHP-FPM
└── default.conf            # Konfigurasi server block Nginx
```

---

## 🏁 Memulai & Cara Instalasi

Anda dapat menjalankan proyek ini dengan dua cara: menggunakan **Docker** (Sangat Direkomendasikan) atau **Manual (XAMPP / Server Lokal)**.

### 🐳 Metode A: Menggunakan Docker (Rekomendasi & Instan)

Metode ini paling mudah karena semua dependensi (PHP, MySQL, Nginx, phpMyAdmin) otomatis dikonfigurasi dan dijalankan dalam container terisolasi.

1.  Pastikan Anda telah memasang [Docker Desktop](https://www.docker.com/products/docker-desktop/) di komputer Anda.
2.  Buka terminal atau Command Prompt pada direktori proyek **Florist**.
3.  Jalankan perintah berikut untuk mengunduh image dan menjalankan container:
    ```bash
    docker compose up -d --build
    ```
4.  Docker akan mengunduh dependensi, menyusun container, dan **mengimpor file `database.sql` secara otomatis**.
5.  Akses layanan melalui browser Anda:
    *   **Aplikasi Web (Nginx):** [http://localhost:8096](http://localhost:8096)
    *   **phpMyAdmin:** [http://localhost:8095](http://localhost:8095) (Untuk mengelola database secara visual)

---

### 💻 Metode B: Instalasi Manual (Menggunakan XAMPP)

Jika Anda ingin menjalankan aplikasi secara tradisional tanpa Docker:

1.  Pasang [XAMPP](https://www.apachefriends.org/) (Rekomendasi versi PHP 8.0 ke atas).
2.  Pindahkan atau salin folder proyek **Florist** ke dalam direktori server lokal Anda:
    *   **Windows:** `C:\xampp\htdocs\Florist`
    *   **macOS:** `/Applications/XAMPP/htdocs/Florist`
3.  Nyalakan modul **Apache** dan **MySQL** melalui XAMPP Control Panel.
4.  Buka **phpMyAdmin** Anda di browser ([http://localhost/phpmyadmin](http://localhost/phpmyadmin)).
5.  Buat database baru bernama `florist`.
6.  Pilih database `florist` tersebut, lalu klik tab **Import** dan pilih file `database.sql` yang terletak di dalam folder proyek untuk mengimpor skema tabel dan contoh data awal.
7.  Sesuaikan pengaturan kredensial database jika diperlukan pada file `Config/database.php`:
    ```php
    $servername = "localhost"; // Ubah menjadi localhost jika di luar Docker
    $username   = "root";      // Default username XAMPP
    $password   = "";          // Default password XAMPP (kosong)
    $database   = "florist";
    ```
8.  Buka browser dan jalankan aplikasi dengan mengunjungi:
    *   [http://localhost/Florist](http://localhost/Florist)

---

## 🔑 Kredensial Login Default

Untuk keperluan pengujian awal, Anda dapat masuk menggunakan akun default berikut:

### 🤠 Akun Administrator
*   **Email:** `admin@florist.com`
*   **Password:** `admin123`

### 👤 Akun Pelanggan (Customer)
Anda dapat mendaftar akun baru secara instan melalui halaman **Register** di aplikasi, atau menggunakan data pelanggan baru setelah mendaftar di portal utama.

---

## 📝 Detail Lisensi

Proyek ini dirilis di bawah lisensi **[MIT License](LICENSE)**. Anda bebas menggunakan, mendistribusikan, dan memodifikasi proyek ini untuk keperluan pribadi maupun komersial dengan tetap menyertakan atribusi pembuat asli.

---

## 🌺 Kontribusi & Masukan

Kontribusi, masukan, dan laporan bug sangat kami hargai!
1. Fork repositori ini.
2. Buat branch fitur baru (`git checkout -b fitur-baru-keren`).
3. Commit perubahan Anda (`git commit -m 'Menambahkan fitur baru yang keren'`).
4. Push ke branch tersebut (`git push origin fitur-baru-keren`).
5. Buat **Pull Request** baru.

*Dibuat dengan 💖 untuk keindahan kelopak bunga di setiap harinya.*
