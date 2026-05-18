<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';


// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: transaction.php");
    exit;
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil detail order
$order_query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    echo "Order tidak ditemukan!";
    exit;
}

// Ambil item produk di order ini
$item_query = $conn->prepare("SELECT oi.*, f.name, f.price, f.image 
    FROM order_items oi 
    JOIN flowers f ON oi.flower_id = f.id 
    WHERE oi.order_id = ?");
$item_query->bind_param("i", $order_id);
$item_query->execute();
$items = $item_query->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/order_detail.css">
</head>
<body>

<header>
  <!-- Kiri: Account -->
  <div class="header-left">
    <a href="<?php echo isset($_SESSION['user_id']) ? 'profilecst.php' : 'auth/login.php'; ?>" 
       class="<?php echo ($current_page == 'profilecst.php') ? 'active' : ''; ?>">
      <img src="../Assets/img/iconprofile.png" alt="Account" width="20"> Account
    </a>
  </div>

    <!-- Tengah: Logo -->
  <div class="header-center">
    <a href="profilecst.php" class="<?php echo ($current_page == 'profilecst.php') ? 'active' : ''; ?>">
      <img src="../Assets/img/flower-logo.png" alt="Logo Florist" class="Logo"> 
    </a>
  </div>

  <!-- Kanan: Transaction + Cart -->
  <div class="header-right">
    <a href="transaction.php" class="<?php echo ($current_page == 'transaction.php') ? 'active' : ''; ?>">
      <img src="../Assets/img/icontrans.png" alt="Transaction" width="20"> My Transaction
    </a>
    <a href="cart.php" class="<?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">
      <img src="../Assets/img/iconkrnj.png" alt="Cart" width="20"> Cart
    </a>
  </div>
</header>


  <body>
<main class="detail-container">
  <h2>Order #<?php echo $order['id']; ?> Details</h2>
  <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($order['order_date'])); ?></p>
  <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
  <p><strong>Total:</strong> Rp<?php echo number_format($order['total_amount'], 3, '.', '.'); ?></p>

  <table class="detail-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td>
          <img src="../Assets/img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-img">
          <?php echo $item['name']; ?>
        </td>
        <td>Rp<?php echo number_format($item['price'], 3, '.', '.'); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>Rp<?php echo number_format($item['price'] * $item['quantity'], 3, '.', '.'); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="transaction.php" class="btn-back">← Back to Orders</a>
</main>
</body>
</html>