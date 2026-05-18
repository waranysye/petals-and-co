<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../config/database.php';


// Ambil semua produk kategori "Mawar"
$sql = "SELECT * FROM flowers WHERE category = 'Bunga Mawar'";
$result = $conn->query($sql);


// Fungsi tambah ke cart
if (isset($_GET['add'])) {
    $flower_id = intval($_GET['add']);

    // Cek apakah produk sudah ada di cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND flower_id=?");
    $check->bind_param("ii", $user_id, $flower_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND flower_id=$flower_id");
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, flower_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $flower_id);
        $stmt->execute();
    }

    header("Location: viewmawar.php");
    exit;
}

// Fungsi kurangi dari cart
if (isset($_GET['remove'])) {
    $flower_id = intval($_GET['remove']);

    $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND flower_id=?");
    $check->bind_param("ii", $user_id, $flower_id);
    $check->execute();
    $res = $check->get_result();
    $cartItem = $res->fetch_assoc();

    if ($cartItem && $cartItem['quantity'] > 1) {
        $conn->query("UPDATE cart SET quantity = quantity - 1 WHERE user_id=$user_id AND flower_id=$flower_id");
    } else {
        $conn->query("DELETE FROM cart WHERE user_id=$user_id AND flower_id=$flower_id");
    }

    header("Location: viewmawar.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/viewmawar.css">
</head>
<body>

<header>
<!-- Kiri: Account -->
<div class="header-left">
  <a href="<?php echo isset($_SESSION['user_id']) ? '../costumer/profilecst.php' : '../costumer/auth/login.php'; ?>" 
     class="<?php echo ($current_page == 'profilecst.php') ? 'active' : ''; ?>">
    <img src="../Assets/img/iconprofile.png" alt="Account" width="20"> Account
  </a>
</div>

    <!-- Tengah: Logo -->
    <div class="header-center">
      <a href="../costumer/profilecst.php" class="<?php echo ($current_page == 'profilecst.php') ? 'active' : ''; ?>">
         <img src="../Assets/img/flower-logo.png" alt="Logo Florist" class="Logo"> 
      </a>
    </div>

<!-- Kanan: Transaction + Chart -->
<div class="header-right">
  <a href="../costumer/transaction.php" class="<?php echo ($current_page == 'transaction.php') ? 'active' : ''; ?>">
    <img src="../Assets/img/icontrans.png" alt="Transaction" width="20"> My Transaction
  </a>
  <a href="../costumer/cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
    <img src="../Assets/img/iconkrnj.png" alt="Cart" width="20"> Cart
    <?php if ($cartCount > 0): ?>
      <span class="cart-badge"><?php echo $cartCount; ?></span>
    <?php endif; ?>
  </a>

</header>

  <!-- Tombol Back -->
  <div class="back-btn">
    <a href="../costumer/idexcostumer.php">← Back</a>
  </div>

  <!-- Judul -->
  <div class="page-header">
    <h1>Roses</h1>
    <p>
      Explore our exquisite collection of roses, perfect for expressing love, appreciation, 
      or celebrating special moments. Each bloom is carefully selected and arranged to create 
      stunning bouquets that convey your heartfelt emotions.
    </p>
  </div>

  <!-- Produk Mawar -->
  <div class="products-grid">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="product-card">
        <td><img src="../Assets/img/<?= $row['image']; ?>" alt="<?php echo $row['name']; ?>">
        <h3><?php echo $row['name']; ?></h3>
        <p class="desc"><?php echo $row['description']; ?></p>
        <p class="price">Rp <?php echo number_format($row['price'], 3, ',', '.'); ?></p>
        <div class="actions">
          <a href="viewmawar.php?remove=<?php echo $row['id']; ?>" class="btn minus">-</a>
          <a href="viewmawar.php?add=<?php echo $row['id']; ?>" class="btn plus">+</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>