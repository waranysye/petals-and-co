<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../Config/database.php';

$message = "";
$message_type = "";

// Pastikan koneksi berhasil
if (!isset($conn)) {
    die("<p style='color:red;'>Koneksi database gagal! Cek path Config/database.php</p>");
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

        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            session_write_close();

            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
                exit();
            } else {
                header("Location: ../costumer/idexcostumer.php");
                exit();
            }
        } else {
            $message = "⚠️ Incorrect password. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "❌ Account not found. Please check your email.";
        $message_type = "error";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Petals & Co</title>
    <meta name="description" content="Sign in to your Petals & Co account and continue your floral journey.">
    <link rel="stylesheet" href="../Assets/css/login.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="auth-wrapper">

    <!-- Left: Image Panel -->
    <div class="auth-image">
        <img src="../Assets/img/login.png" alt="Beautiful peony bouquet — Petals & Co">
        <div class="auth-image-overlay">
            <div class="brand">Petals & Co</div>
            <div class="tagline">Curated floral arrangements for every occasion</div>
        </div>
    </div>

    <!-- Right: Form Panel -->
    <div class="auth-form-panel">
        <div class="auth-form-box">
            <img src="../Assets/img/flower-logo.png" alt="Petals & Co Logo" class="auth-logo">

            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to continue your floral journey.</p>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert-success">✅ Registration successful! Please sign in below.</div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert-<?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-auth">Sign In</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>
