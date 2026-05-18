<?php
session_start();
include '../config/database.php';

$message = "";

// Jika tombol daftar ditekan
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role']; // role dipilih user

    // Validasi role agar hanya 'admin' atau 'customer'
    if (!in_array($role, ['admin', 'costumer'])) {
        $role = 'costumer';
    }

    // Cek apakah email sudah terdaftar
    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        $message = "<p style='color:red;'>Email sudah terdaftar. Silakan login.</p>";
    } else {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Simpan ke database
        $sql = "INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$hashed_password', '$role')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = "<p style='color:red;'>Terjadi kesalahan: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Rosette Florist</title>
    <link rel="stylesheet" href="../Assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <img src="../Assets/img/flower-logo.png" alt="Logo Florist" class="logo">

            <h2>Buat Akun Baru</h2>
            <p>Isi data di bawah untuk bergabung dengan Rosette Florist 🌸</p>

            <?= $message ?>

            <form action="" method="POST">
                <input type="text" name="name" placeholder="Nama Lengkap" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Password" required>

                <!-- Pilihan role -->
                <select name="role" required>
                    <option value="costumer" selected>Costumer</option>
                    <option value="admin">Admin</option>
                </select>

                <input type="submit" name="register" value="Daftar">
            </form>

            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
