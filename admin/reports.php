<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

// Ambil data total dari database
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_products = $conn->query("SELECT COUNT(*) AS total FROM flowers")->fetch_assoc()['total'];
$total_orders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(total_amount) AS revenue FROM orders")->fetch_assoc()['revenue'];

// Data Order Status
$order_pending = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='pending'")->fetch_assoc()['total'];
$order_completed = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='delivered'")->fetch_assoc()['total'];
$order_cancelled = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='cancelled'")->fetch_assoc()['total'];

// Payment Reports (contoh static, bisa kamu sesuaikan)

$payment_reports = $conn->query("
    SELECT 
        payment_method,
        SUM(total_amount) AS amount,
        COUNT(id) AS transactions
    FROM orders
    GROUP BY payment_method
");

// Stock & Best-Selling Products
$best_selling = $conn->query("
    SELECT f.name, f.stock, COALESCE(SUM(oi.quantity),0) as sales
    FROM flowers f
    LEFT JOIN order_items oi ON f.id = oi.flower_id
    GROUP BY f.id
    ORDER BY sales DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <!-- Panggil file CSS -->
  <link rel="stylesheet" href="../Assets/css/reports.css">
</head>
<body>

<!-- Sidebar -->
  <div class="sidebar">
    <h2>My App</h2>
    <ul>
      <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">🏠Dashboard</a></li>
      <li><a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">🌸Produk</a></li>
      <li><a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">👥Orders</a></li>
      <li><a href="reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">📊Laporan</a></li>
      <li><a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">⚙️Akun</a></li>
      <li class="bottom-menu"><a href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>

  <!-- Header -->
  <div class="header">
    <h1>
      <?php 
        // Judul otomatis berdasarkan halaman
        switch($current_page) {
          case 'index.php': echo 'Dashboard'; break;
          case 'products.php': echo 'Produk'; break;
          case 'customers.php': echo 'Pelanggan'; break;
          case 'reports.php': echo 'Laporan'; break;
          case 'profil.php': echo 'Akun'; break;
          default: echo 'Admin Panel';
        }
      ?>
    </h1>
    <div class="admin">
      <span>Admin</span>
       <img src="../Assets/img/spiderman.jpg" alt="Admin">
    </div>
  </div>

  <!-- Main Content -->
<div class="main-content">
  <h2>Summary</h2>
  
  <!-- Top Cards -->
  <div class="summary-cards">
    <div class="card">
      <h3>Total Customers</h3>
      <p><?= number_format($total_customers); ?></p>
    </div>
    <div class="card">
      <h3>Total Products</h3>
      <p><?= number_format($total_products); ?></p>
    </div>
    <div class="card">
      <h3>Total Orders</h3>
      <p><?= number_format($total_orders); ?></p>
    </div>
    <div class="card">
      <h3>Total Revenue</h3>
      <p>Rp<?= number_format($total_revenue, 2); ?></p>
    </div>
  </div>

  <!-- Middle Cards -->
  <div class="middle-cards">
    <div class="card">
      <h3>Order Status</h3>
      <ul class="status-list">
        <li>Pending <span><?= $order_pending; ?></span></li>
        <li>Completed <span><?= $order_completed; ?></span></li>
        <li>Cancelled <span><?= $order_cancelled; ?></span></li>
      </ul>
    </div>
<div class="card">
  <h3>Payment Reports</h3>
  <table>
    <thead>
      <tr>
        <th>Payment Method</th>
        <th>Amount</th>
        <th>Transactions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($pay = $payment_reports->fetch_assoc()): ?>
        <tr>
          <td><?= ucfirst($pay['payment_method']); ?></td>
          <td>Rp<?= number_format($pay['amount'], 2); ?></td>
          <td><?= $pay['transactions']; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
  </div>

  <!-- Bottom Card -->
  <div class="bottom-card">
    <div class="card">
      <h3>Stock & Best-Selling Products</h3>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Stock</th>
            <th>Sales</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $best_selling->fetch_assoc()): ?>
            <tr>
              <td><?= $row['name']; ?></td>
              <td><?= $row['stock']; ?></td>
              <td><?= $row['sales']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>