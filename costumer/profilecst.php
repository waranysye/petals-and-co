<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil jumlah item di keranjang secara dinamis
$cartCount = 0;
$stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$cartCount = $res['total_items'] ? $res['total_items'] : 0;

// Update data kalau ada form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Cek agar data tidak kosong
    if (!empty($name) && !empty($email)) {
        $update = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $update->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
        $update->execute();

        if ($update->affected_rows >= 0) {
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profilecst.php");
            exit;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = "Name and email are required.";
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
  <title>My Profile | Petals & Co</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../Assets/css/profilecst.css">
</head>
<body>

  <!-- Header / Navigation Bar -->
  <header>
    <div class="header-left">
      <a href="idexcostumer.php" class="logo-text">Petals & Co</a>
    </div>

    <nav class="header-center">
      <a href="idexcostumer.php" class="<?php echo ($current_page == 'idexcostumer.php') ? 'active' : ''; ?>">Home</a>
      <a href="collections.php" class="<?php echo ($current_page == 'collections.php') ? 'active' : ''; ?>">Collections</a>
      <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    </nav>

    <div class="header-right">
      <!-- Search Form -->
      <div class="search-container">
        <form action="collections.php" method="GET" class="search-form">
          <input type="text" name="search" placeholder="Search flowers..." required>
          <button type="submit" title="Search">
            <svg viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          </button>
        </form>
      </div>

      <!-- Shopping Bag / Cart -->
      <a href="cart.php" class="cart-icon-link" title="Cart">
        <svg viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <span class="cart-badge" style="<?php echo ($cartCount > 0) ? '' : 'display: none;'; ?>"><?php echo $cartCount; ?></span>
      </a>

      <!-- Akun & Transaksi -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profilecst.php" class="account-link-btn" title="My Account">
          <img src="../Assets/img/iconprofile.png" alt="Profile" width="18"> Account
        </a>
        <a href="transaction.php" class="account-link-btn" title="My Orders">
          <img src="../Assets/img/icontrans.png" alt="Orders" width="18"> Orders
        </a>
      <?php else: ?>
        <a href="../auth/login.php" class="account-link-btn" style="border: 1px solid var(--primary-green); padding: 5px 12px; border-radius: 4px;">Login</a>
      <?php endif; ?>
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
               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
      </div>

      <div class="form-group">
        <label for="address">Address</label>
        <textarea name="address" id="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
      </div>

      <div class="form-group">
        <label for="join">Join Date</label>
        <input type="text" id="join" 
               value="<?php echo date("F j, Y", strtotime($user['created_at'])); ?>" readonly>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="idexcostumer.php" class="btn-cancel">Cancel</a>
        <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?');">🚪 Logout</a>
      </div>
    </form>
  </main>
</body>
</html>
