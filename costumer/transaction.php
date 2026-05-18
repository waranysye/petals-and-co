<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

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
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/transaction.css">
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
        <td colspan="5">Belum ada transaksi.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
</main>
</body>
</html>