<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Tambah produk
if (isset($_GET['add'])) {
    $flower_id = intval($_GET['add']);
    $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND flower_id=$flower_id");
    header("Location: cart.php");
    exit;
}

// Kurangi produk
if (isset($_GET['remove'])) {
    $flower_id = intval($_GET['remove']);
    $check = $conn->query("SELECT * FROM cart WHERE user_id=$user_id AND flower_id=$flower_id");
    $item = $check->fetch_assoc();

    if ($item && $item['quantity'] > 1) {
        $conn->query("UPDATE cart SET quantity = quantity - 1 WHERE user_id=$user_id AND flower_id=$flower_id");
    } else {
        $conn->query("DELETE FROM cart WHERE user_id=$user_id AND flower_id=$flower_id");
    }
    header("Location: cart.php");
    exit;
}
// Ambil semua produk di cart
$sql = "SELECT c.*, f.name, f.price, f.image 
        FROM cart c 
        JOIN flowers f ON c.flower_id = f.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

// Hitung subtotal
$subtotal = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $row['total_price'] = $row['price'] * $row['quantity'];
    $subtotal += $row['total_price'];
    $cart_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/cart.css">
</head>
<body>

<header>
  <!-- Kiri: Account -->
  <div class="header-left">
    <a href="<?php echo isset($_SESSION['user_id']) ? 'profilecst.php' : '../Assets/auth/login.php'; ?>" 
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
      <a href="transaction.php" class="<?php echo ($current_page == 'transaction.php') ? 'active' : ''; ?>">
        <img src="../Assets/img/icontrans.png" alt="Transaction" width="20"> My Transaction
      </a>
      <a href="cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
        <img src="../Assets/img/iconkrnj.png" alt="Chart" width="20"> Chart
      </a>
    </div>
  </header>

    <!-- Tombol Back -->
  <div class="back-btn">
    <a href="../costumer/idexcostumer.php">← Back</a>
  </div>

<div class="cart-container">

  <!-- List Produk -->
  <h2>Your Cart</h2>
  <div class="cart-items">
    <?php if (count($cart_items) > 0): ?>
      <?php foreach ($cart_items as $item): ?>
        <div class="cart-card">
          <img src="../Assets/img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
          <div class="cart-info">
            <h3><?php echo $item['name']; ?></h3>
            <p class="price">Rp <?php echo number_format($item['price'], 3, '.', '.'); ?></p>
          </div>
          <div class="cart-actions">
            <a href="cart.php?remove=<?php echo $item['flower_id']; ?>" class="btn minus">-</a>
            <span><?php echo $item['quantity']; ?></span>
            <a href="cart.php?add=<?php echo $item['flower_id']; ?>" class="btn plus">+</a>
          </div>
          <div class="item-total">
            Rp <?php echo number_format($item['total_price'], 3, '.', '.'); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>

  <!-- Order Summary -->
  <div class="order-summary">
    <h3>Order Summary</h3>
    <ul>
      <?php foreach ($cart_items as $item): ?>
        <li>
          <?php echo $item['name']; ?> x<?php echo $item['quantity']; ?>  
          <span>Rp <?php echo number_format($item['total_price'], 3, '.', '.'); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="summary-row">
      <span>Subtotal</span>
      <span>Rp <?php echo number_format($subtotal, 3, '.', '.'); ?></span>
    </div>
    <div class="summary-row total">
      <span>Total</span>
      <span>Rp <?php echo number_format($subtotal, 3, '.', '.'); ?></span>
    </div>
    <a href="payment.php" class="checkout-btn">Proceed to Checkout</a>
  </div>

</div>

</body>
</html>
