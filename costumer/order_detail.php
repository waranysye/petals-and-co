<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';


// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: transaction.php");
    exit;
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil jumlah item di keranjang secara dinamis
$cartCount = 0;
$stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$cartCount = $res['total_items'] ? $res['total_items'] : 0;

// Ambil detail order
$order_query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found or access denied.";
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
  <title>Order Detail | Petals & Co</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
 <link rel="stylesheet" href="../Assets/css/order_detail.css">
</head>
<body>

  <!-- Header / Navigation Bar -->
  <header>
    <div class="header-left">
      <a href="idexcostumer.php" class="logo-text">Petals & Co</a>
    </div>

    <nav class="header-center">
      <a href="idexcostumer.php" class="<?php echo ($current_page == 'idexcostumer.php') ? 'active' : ''; ?>">Home</a>
      <a href="collections.php" class="<?php echo ($current_page == 'collections.php') ? 'active' : ''; ?>">Collections</a>
      <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    </nav>

    <div class="header-right">
      <!-- Search Form -->
      <div class="search-container">
        <form action="collections.php" method="GET" class="search-form">
          <input type="text" name="search" placeholder="Search flowers..." required>
          <button type="submit" title="Search">
            <svg viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          </button>
        </form>
      </div>

      <!-- Shopping Bag / Cart -->
      <a href="cart.php" class="cart-icon-link" title="Cart">
        <svg viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <span class="cart-badge" style="<?php echo ($cartCount > 0) ? '' : 'display: none;'; ?>"><?php echo $cartCount; ?></span>
      </a>

      <!-- Akun & Transaksi -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profilecst.php" class="account-link-btn" title="My Account">
          <img src="../Assets/img/iconprofile.png" alt="Profile" width="18"> Account
        </a>
        <a href="transaction.php" class="account-link-btn" title="My Orders">
          <img src="../Assets/img/icontrans.png" alt="Orders" width="18"> Orders
        </a>
      <?php else: ?>
        <a href="../auth/login.php" class="account-link-btn" style="border: 1px solid var(--primary-green); padding: 5px 12px; border-radius: 4px;">Login</a>
      <?php endif; ?>
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
