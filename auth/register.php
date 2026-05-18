<?php
session_start();
include '../Config/database.php';

$message = "";
$message_type = "";

// Jika tombol daftar ditekan
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Validasi role agar hanya 'admin' atau 'costumer'
    if (!in_array($role, ['admin', 'costumer'])) {
        $role = 'costumer';
    }

    // Cek apakah email sudah terdaftar
    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        $message = "This email is already registered. Please sign in.";
        $message_type = "error";
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
            $message = "An error occurred: " . $conn->error;
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Petals & Co</title>
    <meta name="description" content="Join Petals & Co and discover the finest curated floral arrangements.">
    <link rel="stylesheet" href="../Assets/css/register.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="auth-wrapper">

    <!-- Left: Image Panel -->
    <div class="auth-image">
        <img src="../Assets/img/register.jpg" alt="Soft white roses — Petals & Co">
        <div class="auth-image-overlay">
            <div class="brand">Petals & Co</div>
            <div class="tagline">Join us and discover floral artistry</div>
        </div>
    </div>

    <!-- Right: Form Panel -->
    <div class="auth-form-panel">
        <div class="auth-form-box">
            <img src="../Assets/img/flower-logo.png" alt="Petals & Co Logo" class="auth-logo">

            <h1 class="auth-title">Create account</h1>
            <p class="auth-subtitle">Join Petals & Co and start your floral journey today.</p>

            <?php if ($message): ?>
                <div class="alert-<?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>

            <form class="auth-form" action="" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required autocomplete="new-password">
                </div>

                <button type="submit" name="register" class="btn-auth">Create Account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>
