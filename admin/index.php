<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();

  // Guard: Ensure user is logged in and is an admin
  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      header("Location: ../auth/login.php");
      exit();
  }

  include '../Config/database.php';


  // =======================
  // Query untuk Dashboard
  // =======================

  // 1. Pendapatan bulan ini
  $q1 = $conn->query("
    SELECT SUM(total_amount) AS pendapatan 
    FROM orders 
    WHERE MONTH(order_date)=MONTH(CURRENT_DATE()) 
    AND YEAR(order_date)=YEAR(CURRENT_DATE())
  ");
  $pendapatan = $q1->fetch_assoc()['pendapatan'] ?? 0;

  // 2. Total produk
  $q2 = $conn->query("SELECT COUNT(*) AS total FROM flowers");
  $total_produk = $q2->fetch_assoc()['total'];

  // 3. Total pemesanan hari ini
  $q3 = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE DATE(order_date) = CURDATE()");
  $total_pemesanan = $q3->fetch_assoc()['total'];

  // 4. Total pelanggan
  $q4 = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
  $total_pelanggan = $q4->fetch_assoc()['total'];

  // 5. Produk Terlaris
  $q5 = $conn->query("
    SELECT f.name, f.category, f.stock, f.price, f.image, SUM(oi.quantity) AS terjual
    FROM order_items oi
    JOIN flowers f ON oi.flower_id = f.id
    GROUP BY f.id
    ORDER BY terjual DESC
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
  <link rel="stylesheet" href="../Assets/css/indexadmin.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Petals & Co</h2>
    <ul>
      <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">🏠 Dashboard</a></li>
      <li><a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">🌸 Products</a></li>
      <li><a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">👥 Orders</a></li>
      <li><a href="reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">📊 Reports</a></li>
      <li><a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">⚙️ Account</a></li>
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
          case 'products.php': echo 'Products'; break;
          case 'customers.php': echo 'Orders'; break;
          case 'reports.php': echo 'Reports'; break;
          case 'profile.php': echo 'Account'; break;
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
    <!-- Cards -->
    <div class="dashboard-cards">
  <div class="card">
    <h3>Revenue This Month</h3>
    <p>Rp <?= number_format($pendapatan,3,'.','.') ?></p>
  </div>
  <div class="card">
    <h3>Total Products</h3>
    <p><?= $total_produk ?></p>
  </div>
  <div class="card">
    <h3>Today's Orders</h3>
    <p><?= $total_pemesanan ?></p>
  </div>
  <div class="card">
    <h3>Total Customers</h3>
    <p><?= $total_pelanggan ?></p>
  </div>
</div>

    <!-- Table Produk Terlaris -->
    <div class="table-container">
  <h3>Best Sellers</h3>
  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Stock</th>
        <th>Price</th>
        <th>Sales</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $q5->fetch_assoc()): ?>
      <tr>
        <td><img src="../Assets/img/<?= $row['image'] ?>" alt="<?= $row['name'] ?>" width="50"></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['category'] ?></td>
        <td><?= $row['stock'] ?></td>
        <td>Rp <?= number_format($row['price'],3,'.','.') ?></td>
        <td><?= $row['terjual'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
  </div>


</body>
</html>
