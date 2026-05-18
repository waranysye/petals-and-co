<?php
// Ambil nama file yang sedang dibuka
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
include '../Config/database.php';

// Query produk kategori Mawar
$sql = "SELECT * FROM flowers WHERE category = 'Bunga Mawar'";
$result_products = $conn->query($sql);

// Hitung jumlah jenis produk di keranjang
$count_cart = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $result_cart = $conn->query("SELECT COUNT(*) AS total FROM cart WHERE user_id = $uid");
    $row_cart = $result_cart->fetch_assoc();
    $count_cart = $row_cart['total'];
}

$products = [];
if ($result_products && $result_products->num_rows > 0) {
    while ($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roses | Rosette Florist</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/viewmawar.css">
</head>
<body>

<header>
  <!-- Kiri: Account -->
  <div class="header-left">
    <a href="<?php echo isset($_SESSION['user_id']) ? '../costumer/profilecst.php' : '../auth/login.php'; ?>" 
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

  <!-- Kanan: Transaction + Cart -->
  <div class="header-right">
    <a href="../costumer/transaction.php" class="<?php echo ($current_page == 'transaction.php') ? 'active' : ''; ?>">
      <img src="../Assets/img/icontrans.png" alt="Transaction" width="20"> My Transaction
    </a>
    <a href="../costumer/cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>" style="position: relative;">
      <img src="../Assets/img/iconkrnj.png" alt="Cart" width="20"> Cart
      <?php if ($count_cart > 0): ?>
        <span class="cart-badge"><?= $count_cart; ?></span>
      <?php endif; ?>
    </a>
  </div>
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
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $row): ?>
        <div class="product-card">
          <img src="../Assets/img/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
          <h3><?= htmlspecialchars($row['name']); ?></h3>
          <p class="desc"><?= htmlspecialchars($row['description']); ?></p>
          <p class="price">Rp <?= number_format($row['price'], 3, '.', '.'); ?></p>
          <div class="actions">
            <button class="btn minus" data-id="<?= $row['id']; ?>" data-action="remove">-</button>
            <button class="btn plus" data-id="<?= $row['id']; ?>" data-action="add">+</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-product">Belum ada produk Roses tersedia.</p>
    <?php endif; ?>
  </div>

<script>
document.querySelectorAll('.actions .btn').forEach(button => {
  button.addEventListener('click', function() {
    const flowerId = this.getAttribute('data-id');
    const action = this.getAttribute('data-action');

    fetch('update_cart.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `flower_id=${flowerId}&action=${action}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        // Update badge jumlah cart
        let badge = document.querySelector('.cart-badge');
        if (!badge) {
          const cartLink = document.querySelector('.header-right a[href="../costumer/cart.php"]');
          badge = document.createElement('span');
          badge.className = 'cart-badge';
          cartLink.appendChild(badge);
        }
        badge.textContent = data.cart_count > 0 ? data.cart_count : '';
      } else {
        alert('Silakan login terlebih dahulu.');
      }
    });
  });
});
</script>

</body>
</html>
