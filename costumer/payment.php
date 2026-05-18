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

$user_id = $_SESSION['user_id'];

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
            $error_message = "Stok untuk produk <strong>{$item['name']}</strong> tidak mencukupi. 
                             Stok tersedia: {$item['stock']}, jumlah yang dipesan: {$item['quantity']}.";
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
            $itemQuery = $conn->prepare("INSERT INTO order_items (order_id, flower_id, quantity) VALUES (?, ?, ?)");
            $itemQuery->bind_param("iii", $order_id, $item['flower_id'], $item['quantity']);
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
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/payment.css">
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
    <p><strong>Nama:</strong> <?php echo $user['name']; ?></p>
    <p><strong>Alamat:</strong> <?php echo $user['address']; ?></p>
    <p><strong>No HP:</strong> <?php echo $user['phone']; ?></p>
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
