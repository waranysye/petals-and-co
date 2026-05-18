<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';

// Cek apakah user sudah login
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

// Ambil data orders user
$query = $conn->prepare("SELECT id, order_date, status, total_amount FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders | Petals & Co</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../Assets/css/transaction.css">
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
    <table class="order-table">
  <thead>
    <tr>
      <th>ORDER</th>
      <th>DATE</th>
      <th>STATUS</th>
      <th>TOTAL</th>
      <th>ACTION</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($orders) > 0): ?>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?php echo $order['id']; ?></td>
          <td><?php echo date("F j, Y", strtotime($order['order_date'])); ?></td>
          <td>
            <span class="status 
              <?php 
                if ($order['status'] == 'delivered') echo 'delivered';
                elseif ($order['status'] == 'shipped') echo 'shipped';
                elseif ($order['status'] == 'cancelled') echo 'cancelled';
                else echo 'pending';
              ?>">
              <?php echo ucfirst($order['status']); ?>
            </span>
          </td>
          <td>Rp<?php echo number_format($order['total_amount'], 3, '.', '.'); ?></td>
          <td>
            <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-detail">View Details</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" style="text-align:center; padding: 32px; color: #716f6b;">You have no orders yet. <a href="collections.php">Start shopping</a></td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</main>
</body>
</html>
