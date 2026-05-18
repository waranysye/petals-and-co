<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

// Ambil nama file
$current_page = basename($_SERVER['PHP_SELF']);

// Misalnya id admin = 1 (dari insert default)
$admin_id = 1;

// Ambil data admin
$query = $conn->query("SELECT * FROM users WHERE id = $admin_id AND role = 'admin'");
$admin = $query->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <!-- Panggil file CSS -->
  <link rel="stylesheet" href="../Assets/css/profileadmin.css">
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

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert-success">
      ✅ Profil berhasil diperbarui!
    </div>
  <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div class="alert-error">
      ❌ Gagal memperbarui profil. Silakan coba lagi.
    </div>
  <?php endif; ?>

    <div class="profile-container">
      <h2>Informasi Profil</h2>
      <table>
        <tr>
          <th>Nama</th>
          <td><?= $admin['name']; ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?= $admin['email']; ?></td>
        </tr>
        <tr>
          <th>No. Telepon</th>
          <td><?= $admin['phone'] ?: '-'; ?></td>
        </tr>
        <tr>
          <th>Role</th>
          <td><?= ucfirst($admin['role']); ?></td>
        </tr>
        <tr>
          <th>Dibuat Pada</th>
          <td><?= date("d M Y", strtotime($admin['created_at'])); ?></td>
        </tr>
      </table>

      <div class="action">
        <a href="edit_profileadmin.php?id=<?= $admin['id']; ?>" class="btn-edit">Edit Profil</a>
      </div>
    </div>
  </div>

</body>
</html>