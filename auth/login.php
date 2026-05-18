<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../Config/database.php';

$message = "";

// Pastikan koneksi berhasil
if (!isset($conn)) {
    die("<p style='color:red;'>Koneksi database gagal! Cek path config/database.php</p>");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("<p style='color:red;'>Query error: " . mysqli_error($conn) . "</p>");
    }

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

       

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            session_write_close();


 // redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
                exit();
            } else {
                header("Location: ../costumer/idexcostumer.php");
                exit();
            }

        } else {
            $message = "<p style='color:red;'>⚠️ Password salah!</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Akun tidak ditemukan!</p>";
    }
}
ob_end_flush();
?>

<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Login | Rosette Florist</title> 
    <link rel="stylesheet" href="../Assets/css/login.css">
     <link rel="stylesheet" href="../Assets/css/style.css"> 
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"> 
    </head> <body> <div class="login-container"> <div class="login-box"> 
    <img src="../Assets/img/flower-logo.png" alt="Logo Florist" class="logo"> 
    <h2>Welcome Back</h2> 
    <p>Log in to continue your floral journey 🌸</p> 
    
<?php if (isset($_GET['registered'])) 
    { echo "<p style='color:green;'>Pendaftaran berhasil! Silakan login.</p>"; } echo $message; ?>
     <form method="POST" action=""> 
        <input type="email" name="email" placeholder="Email" required>
         <input type="password" name="password" placeholder="Password" required> 
         <input type="submit" value="Login"> </form> 
         <div class="signup"> Belum punya akun? <a href="register.php">Daftar di sini</a> 
        </div> 
    </div> 
</div> 
<script src="../assets/js/main.js"></script> 
</body> 
</html>