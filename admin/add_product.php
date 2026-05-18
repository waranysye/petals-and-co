<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock']; // Tambah stok

    // Upload image
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../Assets/img/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    // Tambahkan stok ke database
    $stmt = $conn->prepare("INSERT INTO flowers (name, description, price, category, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $name, $description, $price, $category, $stock, $image);

    if ($stmt->execute()) {
        header("Location: products.php?added=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

if (isset($_GET['hapus_produk'])) {
    $id = $_GET['hapus_produk'];
    mysqli_query($conn, "DELETE FROM produk WHERE produk_id='$id'");
    echo "<script>alert('Produk berhasil dihapus!'); window.location='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Flower Product</title>
  <link rel="stylesheet" href="../Assets/css/add_product.css">
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

  <div class="main-content">
  <div class="card">
    <h2>Add New Flower Product</h2>
    <p class="subtitle">Fill in the details below to add a new product to your inventory.</p>

    <form method="POST" enctype="multipart/form-data">
      <label for="name">Product Name</label>
      <input type="text" id="name" name="name" placeholder="e.g., 'Velvet Kiss Roses'" required>

      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Describe the flower arrangement..." required></textarea>

      <div class="grid">
        <div>
          <label for="price">Price</label>
           <input type="text" id="price" name="price" placeholder="0" required>
        </div>
        <div>
        <script>
  const priceInput = document.getElementById("price");

  priceInput.addEventListener("input", function(e) {
    let value = this.value.replace(/\D/g, ""); // buang non angka
    value = new Intl.NumberFormat('id-ID').format(value); // format ribuan
    this.value = value;
  });
</script>
          <label for="category">Category</label>
          <select id="category" name="category" required>
            <option value="Bunga Matahari">Bunga Matahari</option>
            <option value="Bunga Mawar">Bunga Mawar</option>
            <option value="Bunga Anggrek">Bunga Anggrek</option>
          </select>
        </div>
      </div>

      <!-- Tambahkan stok -->
<div class="grid">
  <div>
    <label for="stock">Stock</label>
    <input type="number" id="stock" name="stock" placeholder="Jumlah stok tersedia" required>
  </div>
</div>


      <label for="image">Product Image</label>
      <div class="file-upload">
        <input type="file" id="image" name="image" accept="image/*" required>
        <p>Upload a file or drag and drop<br><span>PNG, JPG, GIF up to 10MB</span></p>
      </div>

      <div class="actions">
        <a href="products.php" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-add">Add Product</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>