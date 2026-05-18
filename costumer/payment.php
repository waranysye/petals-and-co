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

$user_id = $_SESSION['user_id'];

// Ambil jumlah item di keranjang secara dinamis
$cartCount = 0;
$stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$cartCount = $res['total_items'] ? $res['total_items'] : 0;

// Ambil data user
$userQuery = $conn->prepare("SELECT name, address, phone FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();

// Ambil data cart user
$cartQuery = $conn->prepare("SELECT c.*, f.name, f.price, f.image, f.stock, f.id as flower_id
                             FROM cart c 
                             JOIN flowers f ON c.flower_id = f.id 
                             WHERE c.user_id = ?");
$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$cartItems = $cartQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total = $subtotal; 

// Jika klik Pay Now
$success = false;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];

    // Validasi stok
    foreach ($cartItems as $item) {
        if ($item['quantity'] > $item['stock']) {
            $error_message = "Insufficient stock for <strong>{$item['name']}</strong>. Available: {$item['stock']}, ordered: {$item['quantity']}.";
            break;
        }
    }

    if (empty($error_message)) {
        // Simpan ke tabel orders
        $orderQuery = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'processing')");
        $orderQuery->bind_param("id", $user_id, $total);
        $orderQuery->execute();
        $order_id = $orderQuery->insert_id;

        // Simpan detail order_items dan kurangi stok
        foreach ($cartItems as $item) {
            $subtotalItem = $item['price'] * $item['quantity'];
            $itemQuery = $conn->prepare("INSERT INTO order_items (order_id, flower_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $itemQuery->bind_param("iiid", $order_id, $item['flower_id'], $item['quantity'], $subtotalItem);
            $itemQuery->execute();

            // Update stok bunga
            $newStock = $item['stock'] - $item['quantity'];
            $stockQuery = $conn->prepare("UPDATE flowers SET stock = ? WHERE id = ?");
            $stockQuery->bind_param("ii", $newStock, $item['flower_id']);
            $stockQuery->execute();
        }

        // Simpan payment
        $paymentQuery = $conn->prepare("INSERT INTO payments (order_id, payment_method, payment_status) VALUES (?, ?, 'unpaid')");
        $paymentQuery->bind_param("is", $order_id, $payment_method);
        $paymentQuery->execute();

        // Hapus cart setelah checkout
        $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCart->bind_param("i", $user_id);
        $clearCart->execute();

        $success = true;
        $order_number = $order_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Petals & Co</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../Assets/css/payment.css">
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
<main class="payment-container">

<?php if (!$success): ?>
  <h2>Payment Details</h2>

  <?php if (!empty($error_message)): ?>
    <div class="error-box"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <div class="order-summary">
    <h3>Order Summary</h3>
    <ul>
      <?php foreach ($cartItems as $item): ?>
        <li>
          <img src="../Assets/img/<?php echo $item['image']; ?>" width="50">
          <?php echo $item['name']; ?> x <?php echo $item['quantity']; ?>  
          <span>Rp<?php echo number_format($item['price'] * $item['quantity'], 3, '.', '.'); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Subtotal:</strong> Rp<?php echo number_format($subtotal, 3, '.', '.'); ?></p>
    <p><strong>Total:</strong> Rp<?php echo number_format($total, 3, '.', '.'); ?></p>
  </div>

  <div class="billing-info">
    <h3>Billing Address</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? ''); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Not provided — update in your profile'); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
  </div>

  <form method="post">
    <h3>Payment Method</h3>
    <label><input type="radio" name="payment_method" value="COD" required> Cash on Delivery</label><br>
    <label><input type="radio" name="payment_method" value="transfer"> Bank Transfer</label><br><br>

    <button type="submit" class="btn-pay">Pay Now</button>
  </form>

<?php else: ?>
  <!-- Thank You Page -->
  <div class="thankyou-card">
    <div class="check-icon">✔</div>
    <h2>Thank You!</h2>
    <p>Your order has been confirmed.</p>
    <p>Your order number is: <strong><?php echo $order_number; ?></strong></p>
    <a href="transaction.php" class="btn-transaction">My Transaction</a>
  </div>
<?php endif; ?>

</main>
</body>
</html>
