<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

// Ambil ID admin dari query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data admin berdasarkan ID
$query = $conn->query("SELECT * FROM users WHERE id = $id AND role = 'admin'");
$admin = $query->fetch_assoc();

if (!$admin) {
  die("Data admin tidak ditemukan!");
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = $_POST['password'];

  // Jika password diisi → update dengan hash, kalau tidak → biarkan password lama
  if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
    $update->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $id);
  } else {
    $update = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
    $update->bind_param("sssi", $name, $email, $phone, $id);
  }

  if ($update->execute()) {
    header("Location: profile.php?success=1");
    exit;
  } else {
    echo "Gagal update data: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil Admin</title>
  <link rel="stylesheet" href="../Assets/css/edit_profileadmin.css">
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
    <div class="form-container">
      <h2>Form Edit Profil</h2>
      <form action="" method="POST">
        <div class="form-group">
          <label for="name">Nama</label>
          <input type="text" name="name" id="name" value="<?= htmlspecialchars($admin['name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email']); ?>" required>
        </div>

        <div class="form-group">
          <label for="phone">No. Telepon</label>
          <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($admin['phone']); ?>">
        </div>

        <div class="form-group">
          <label for="password">Password (kosongkan jika tidak diubah)</label>
          <input type="password" name="password" id="password">
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Save</button>
          <a href="profile.php" class="btn-back">Back</a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>