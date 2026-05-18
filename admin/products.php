<?php
session_start();
include '../config/database.php';

// Ambil nama file yang sedang dibuka
$current_page = basename($_SERVER['PHP_SELF']);

// Query ambil data produk
$result = $conn->query("SELECT * FROM flowers");

// Fungsi helper untuk format rupiah
function rupiah($angka){
    return "Rp " . number_format($angka, 3, '.', '.');
}

if (isset($_GET['hapus_produk'])) {
    $id = intval($_GET['hapus_produk']); 
    $conn->query("DELETE FROM flowers WHERE id = $id");
    echo "<script>alert('Produk berhasil dihapus!'); window.location='products.php';</script>";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <!-- Panggil file CSS -->
  <link rel="stylesheet" href="../Assets/css/productsadmin.css">
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

  <?php if (isset($_GET['success'])): ?>
  <div class="alert success">
    <?= htmlspecialchars($_GET['success']); ?>
  </div>
<?php elseif (isset($_GET['error'])): ?>
  <div class="alert error">
    <?= htmlspecialchars($_GET['error']); ?>
  </div>
<?php endif; ?>


   <!-- Main Content -->
  <div class="main-content">
    <div class="top-bar">
      <a href="add_product.php" class="btn-add">+ Tambah Produk</a>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Kategori</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['name']; ?></td>
            <td><img src="../Assets/img/<?= $row['image']; ?>" alt="<?= $row['name']; ?>" width="50"></td>
            <td><?= rupiah($row['price']); ?></td>
            <td><?= $row['stock']; ?></td>
            <td><?= $row['category']; ?></td>
            <td>
              <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
              <a href="products.php?hapus_produk=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin hapus produk ini?');">Hapus</a>

            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

<script>
  // otomatis hilang setelah 3 detik
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.style.transition = "opacity 0.5s";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }
  }, 3000);
</script>


</body>
</html>