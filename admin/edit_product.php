<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

// Pastikan ada ID produk
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = $_GET['id'];

// Ambil data produk
$stmt = $conn->prepare("SELECT * FROM flowers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Produk tidak ditemukan!";
    exit;
}

// Update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $category    = $_POST['category'];

    // Upload gambar baru jika ada
    $image = $product['image']; 
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../Assets/img/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="../Assets/css/edit_product.css">
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
      <li class="bottom-menu"><a href="auth/login.php">Logout</a></li>
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

<div class="main-content">
  <div class="card">
    <h2>Edit Product</h2>
    <p class="subtitle">Update product details below.</p>

    <form method="POST" enctype="multipart/form-data">
      <label for="name">Product Name</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

      <label for="description">Description</label>
      <textarea id="description" name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

      <div class="grid">
        <div>
          <label for="price">Price</label>
          <input type="number" step="0.01" id="price" name="price" value="<?= $product['price']; ?>" required>
        </div>
        <div>
          <label for="stock">Stock</label>
          <input type="number" id="stock" name="stock" value="<?= $product['stock']; ?>" required>
        </div>
      </div>

      <label for="category">Category</label>
      <select id="category" name="category" required>
        <option value="Bunga Matahari" <?= ($product['category'] == 'Bunga Matahari') ? 'selected' : ''; ?>>Bunga Matahari</option>
        <option value="Bunga Mawar" <?= ($product['category'] == 'Bunga Mawar') ? 'selected' : ''; ?>>Bunga Mawar</option>
        <option value="Bunga Anggrek" <?= ($product['category'] == 'Bunga Anggrek') ? 'selected' : ''; ?>>Bunga Anggrek</option>
      </select>

      <label for="image">Product Image</label>
      <div class="file-upload">
        <input type="file" id="image" name="image" accept="image/*">
        <?php if ($product['image']): ?>
          <p>Current Image:</p>
          <img src="../Assets/img/<?= htmlspecialchars($product['image']); ?>" alt="Product Image" width="100">
        <?php endif; ?>
      </div>

      <div class="actions">
        <a href="products.php" class="btn-cancel">Discard</a>
        <button type="submit" class="btn-update">Update Product</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>

