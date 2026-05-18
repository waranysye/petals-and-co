<?php

// Konfigurasi koneksi
$servername = "mysql";   // server database (default: localhost)
$username   = "appuser";        // username MySQL (default XAMPP: root)
$password   = "password";            // password MySQL (kosong untuk XAMPP)
$database   = "florist";     // nama database kamu

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Jika berhasil (tidak wajib ditampilkan, tapi bisa untuk debugging awal)
// echo "Koneksi ke database berhasil!";
?>