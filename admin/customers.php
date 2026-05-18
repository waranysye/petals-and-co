<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

 // Query ambil data orders + customer
  $query = "
    SELECT o.id, u.name AS customer_name, o.order_date, o.total_amount, o.status
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
  ";
  $result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers - Admin Dashboard</title>
  <link rel="stylesheet" href="../Assets/css/customers.css">
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
    <h2>Manage all your customer orders</h2>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID Pesanan</th>
            <th>Nama Customer</th>
            <th>Tanggal Pesanan</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                <td><?= date("d M Y H:i", strtotime($row['order_date'])); ?></td>
                <td>Rp <?= number_format($row['total_amount'], 3, '.', '.'); ?></td>
                <td><?= ucfirst($row['status']); ?></td>
                <td><a href="viewdetailadmin.php?id=<?= $row['id']; ?>" class="btn-view">View Detail</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">Belum ada pesanan</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>