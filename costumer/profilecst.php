<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Update data kalau ada form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Cek agar data tidak kosong
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($address)) {
        $update = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $update->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
        $update->execute();

        if ($update->affected_rows >= 0) {
            $_SESSION['success'] = "Profil berhasil diperbarui!";
            header("Location: profilecst.php");
            exit;
        } else {
            $error = "Gagal memperbarui data.";
        }
    } else {
        $error = "Semua field harus diisi!";
    }
}

// Ambil data user setelah update
$query = $conn->prepare("SELECT name, email, phone, address, created_at FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/profilecst.css">
</head>
<body>

  <header>
    <!-- Kiri: Account -->
    <div class="header-left">
      <a href="profilecst.php" class="<?php echo ($current_page == 'profilecst.php') ? 'active' : ''; ?>">
        <img src="../Assets/img/iconprofile.png" alt="Account" width="20"> Account
      </a>
    </div>

    <!-- Tengah: Logo -->
    <div class="header-center">
      <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
         <img src="../Assets/img/flower-logo.png" alt="Logo Florist" class="Logo"> 
      </a>
    </div>

    <!-- Kanan: Transaction + Chart -->
    <div class="header-right">
      <a href="transaction.php" class="<?php echo ($current_page == 'transaction.php') ? 'active' : ''; ?>">
        <img src="../Assets/img/icontrans.png" alt="Transaction" width="20"> My Transaction
      </a>
      <a href="cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
        <img src="../Assets/img/iconkrnj.png" alt="Chart" width="20"> Chart
      </a>
    </div>
  </header>

      <!-- Tombol Back -->
  <div class="back-btn">
    <a href="../costumer/idexcostumer.php">← Back</a>
  </div>

<body>
  <main class="profile-container">
    <h2>My Account</h2>

    <?php if (isset($_SESSION['success'])): ?>
      <p class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="profile-card">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" 
               value="<?php echo htmlspecialchars($user['name']); ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" 
               value="<?php echo htmlspecialchars($user['email']); ?>" required>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" 
               value="<?php echo htmlspecialchars($user['phone']); ?>" required>
      </div>

      <div class="form-group">
        <label for="address">Address</label>
        <textarea name="address" id="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
      </div>

      <div class="form-group">
        <label for="join">Join Date</label>
        <input type="text" id="join" 
               value="<?php echo date("F j, Y", strtotime($user['created_at'])); ?>" readonly>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="indexcustomer.php" class="btn-cancel">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
