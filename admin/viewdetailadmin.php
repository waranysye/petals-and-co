<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);

  session_start();
include '../config/database.php';

if (!isset($_GET['id'])) {
  header("Location: customers.php");
  exit;
}

$order_id = intval($_GET['id']);

// Ambil data order
$orderQuery = $conn->query("
  SELECT o.*, u.name AS customer_name, u.email, u.phone, u.address
  FROM orders o
  JOIN users u ON o.user_id = u.id
  WHERE o.id = $order_id
");
$order = $orderQuery->fetch_assoc();

// Ambil item produk dalam order
$itemsQuery = $conn->query("
  SELECT oi.*, f.name, f.price
  FROM order_items oi
  JOIN flowers f ON oi.flower_id = f.id
  WHERE oi.order_id = $order_id
");

// Handle update order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = $_POST['status'];
  $notes = $_POST['notes'];

  $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");

  // Catat notes ke tabel order_notes kalau ada (optional bikin tabel order_notes)
  // $conn->query("INSERT INTO order_notes (order_id, notes) VALUES ($order_id, '$notes')");

  header("Location: customers.php?updated=1");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Detail</title>
  <link rel="stylesheet" href="../Assets/css/viewdetailadmin.css">
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
<!-- Back Button -->
  <a href="customers.php" class="btn-back">← Back</a>

  <div class="content-grid">

    <!-- Order Details -->
    <div class="card order-details">
      <h2>Order Details</h2>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $subtotal = 0;
          while($item = $itemsQuery->fetch_assoc()): 
            $lineTotal = $item['quantity'] * $item['price'];
            $subtotal += $lineTotal;
          ?>
          <tr>
            <td><?= htmlspecialchars($item['name']); ?></td>
            <td><?= $item['quantity']; ?></td>
            <td>Rp <?= number_format($item['price'],3,'.','.'); ?></td>
            <td>Rp <?= number_format($lineTotal,3,'.','.'); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <div class="totals">
        <p><strong>Subtotal:</strong> Rp <?= number_format($subtotal,3,'.','.'); ?></p>
        <p><strong>Total:</strong> Rp <?= number_format($order['total_amount'],3,'.','.'); ?></p>
      </div>
    </div>

    <!-- Customer Info -->
    <div class="card customer-info">
      <h2>Customer Information</h2>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
      <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
    </div>

    <!-- Timeline -->
    <div class="card order-timeline">
      <h2>Order Timeline</h2>
      <ul>
        <li><span class="<?= ($order['status']!='pending')?'done':''; ?>">Order Placed</span> (<?= date("d M Y H:i", strtotime($order['order_date'])); ?>)</li>
        <li><span class="<?= ($order['status']=='shipped'||$order['status']=='delivered')?'done':''; ?>">Order Shipped</span></li>
        <li><span class="<?= ($order['status']=='delivered')?'done':''; ?>">Delivered</span></li>
      </ul>
    </div>

    <!-- Update Order -->
    <div class="card update-order">
      <h2>Update Order</h2>
      <form method="POST">
        <label for="status">Order Status</label>
        <select name="status" id="status">
          <option value="pending" <?= ($order['status']=='pending')?'selected':''; ?>>Pending</option>
          <option value="processing" <?= ($order['status']=='processing')?'selected':''; ?>>Processing</option>
          <option value="shipped" <?= ($order['status']=='shipped')?'selected':''; ?>>Shipped</option>
          <option value="delivered" <?= ($order['status']=='delivered')?'selected':''; ?>>Delivered</option>
          <option value="cancelled" <?= ($order['status']=='cancelled')?'selected':''; ?>>Cancelled</option>
        </select>

        <label for="notes">Order Notes</label>
        <textarea name="notes" id="notes" placeholder="Add a note for the customer or internal records..."></textarea>

        <button type="submit" class="btn-update">Update Order</button>
      </form>
    </div>

  </div>
</div>

</body>
</html>