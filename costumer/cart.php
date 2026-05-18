<?php
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
  include '../Config/database.php';

  // Pastikan user login
  if (!isset($_SESSION['user_id'])) {
      header("Location: ../auth/login.php");
      exit;
  }
  $user_id = $_SESSION['user_id'];

  // Ambil jumlah item di keranjang
  $cartCount = 0;
  $stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $cartCount = $res['total_items'] ? $res['total_items'] : 0;

  // Tambah produk
  if (isset($_GET['add'])) {
      $flower_id = intval($_GET['add']);
      $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND flower_id=$flower_id");
      header("Location: cart.php");
      exit;
  }

  // Kurangi / hapus produk
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

  // Hapus item sepenuhnya
  if (isset($_GET['delete'])) {
      $flower_id = intval($_GET['delete']);
      $conn->query("DELETE FROM cart WHERE user_id=$user_id AND flower_id=$flower_id");
      header("Location: cart.php");
      exit;
  }

  // Ambil semua produk di cart
  $sql = "SELECT c.*, f.name, f.price, f.image, f.category
          FROM cart c
          JOIN flowers f ON c.flower_id = f.id
          WHERE c.user_id = $user_id";
  $result = $conn->query($sql);

  $subtotal = 0;
  $cart_items = [];
  while ($row = $result->fetch_assoc()) {
      $row['total_price'] = $row['price'] * $row['quantity'];
      $subtotal += $row['total_price'];
      $cart_items[] = $row;
  }

  // Order summary calculations
  $shipping = 45000;
  $tax = round($subtotal * 0.01);
  $total = $subtotal + $shipping + $tax;

  // Ambil produk lain sebagai "Complete the Gift" suggestions (exclude items already in cart)
  $cart_ids = array_column($cart_items, 'flower_id');
  $exclude = count($cart_ids) > 0 ? implode(',', $cart_ids) : '0';
  $gift_sql = "SELECT id, name, price, image FROM flowers WHERE id NOT IN ($exclude) LIMIT 4";
  $gift_result = $conn->query($gift_sql);
  $gift_items = [];
  if ($gift_result) {
      while ($g = $gift_result->fetch_assoc()) {
          $gift_items[] = $g;
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart | Petals & Co</title>
  <meta name="description" content="Review and manage your Petals & Co shopping bag.">
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../Assets/css/cart.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- ── Header / Navigation ── -->
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
      <div class="search-container">
        <form action="collections.php" method="GET" class="search-form">
          <input type="text" name="search" placeholder="Search flowers..." required>
          <button type="submit" title="Search">
            <svg viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          </button>
        </form>
      </div>

      <a href="cart.php" class="cart-icon-link" title="Cart">
        <svg viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <span class="cart-badge" style="<?php echo ($cartCount > 0) ? '' : 'display: none;'; ?>"><?php echo $cartCount; ?></span>
      </a>

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

  <!-- ── Page Title ── -->
  <div class="cart-page-wrapper">
    <div class="cart-page-intro">
      <h1>Your Shopping Bag</h1>
      <p>Thoughtfully selected blooms, ready for their journey.</p>
    </div>
  </div>

  <?php if (count($cart_items) > 0): ?>

  <!-- ── Main Two-Column Layout ── -->
  <div class="cart-layout">

    <!-- ── LEFT: Cart Items ── -->
    <div class="cart-items-col">

      <?php foreach ($cart_items as $item): ?>
      <div class="cart-card">
        <!-- Remove button -->
        <a href="cart.php?delete=<?php echo $item['flower_id']; ?>" class="cart-remove-btn" title="Remove item">&#x2715;</a>

        <!-- Product image -->
        <img
          src="../Assets/img/<?php echo htmlspecialchars($item['image']); ?>"
          alt="<?php echo htmlspecialchars($item['name']); ?>"
          class="cart-card-img"
        >

        <!-- Info + controls -->
        <div class="cart-card-body">
          <h3 class="cart-card-name"><?php echo htmlspecialchars($item['name']); ?></h3>
          <p class="cart-card-variant"><?php echo strtoupper(htmlspecialchars($item['category'] ?? 'Fresh Arrangement')); ?></p>

          <div class="cart-qty-row">
            <!-- Quantity controls -->
            <div class="qty-controls">
              <a href="cart.php?remove=<?php echo $item['flower_id']; ?>" class="qty-btn">&#x2212;</a>
              <span class="qty-value"><?php echo $item['quantity']; ?></span>
              <a href="cart.php?add=<?php echo $item['flower_id']; ?>" class="qty-btn">&#x2B;</a>
            </div>

            <!-- Item total -->
            <div class="cart-item-price">
              <span class="cart-item-price-main">Rp <?php echo number_format($item['total_price'], 3, '.', '.'); ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- ── Complete the Gift ── -->
      <?php if (count($gift_items) > 0): ?>
      <div class="complete-gift">
        <h2>Complete the Gift</h2>
        <div class="gift-items-row">
          <?php foreach ($gift_items as $g): ?>
          <div class="gift-item">
            <a href="collections.php?add_to_cart=<?php echo $g['id']; ?>">
              <img src="../Assets/img/<?php echo htmlspecialchars($g['image']); ?>" alt="<?php echo htmlspecialchars($g['name']); ?>">
            </a>
            <p class="gift-item-name"><?php echo htmlspecialchars($g['name']); ?></p>
            <p class="gift-item-price">Rp <?php echo number_format($g['price'], 3, '.', '.'); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- /cart-items-col -->

    <!-- ── RIGHT: Order Summary ── -->
    <div class="cart-summary-col">
      <div class="order-summary-box">
        <h3>Order Summary</h3>

        <div class="summary-line">
          <span>Subtotal</span>
          <span>Rp <?php echo number_format($subtotal, 3, '.', '.'); ?></span>
        </div>
        <div class="summary-line">
          <span>Shipping</span>
          <span>Rp <?php echo number_format($shipping, 3, '.', '.'); ?></span>
        </div>
        <div class="summary-line">
          <span>Estimated Tax</span>
          <span>Rp <?php echo number_format($tax, 3, '.', '.'); ?></span>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-total">
          <span class="label">Total</span>
          <span class="amount">Rp <?php echo number_format($total, 3, '.', '.'); ?></span>
        </div>

        <!-- Promo Code -->
        <div class="promo-row">
          <input type="text" placeholder="Promo code" id="promo-input">
          <button class="promo-apply-btn">APPLY</button>
        </div>

        <!-- Checkout -->
        <a href="payment.php" class="checkout-btn">PROCEED TO PAYMENT</a>
        <a href="collections.php" class="continue-shopping-link">Continue Shopping</a>

        <!-- Delivery Info -->
        <div class="delivery-info-box">
          <div class="delivery-icon">🚚</div>
          <div>
            <strong>Standard Delivery</strong>
            <p>Arriving in 2–3 business days. Same-day delivery available for premium orders before 12 PM.</p>
          </div>
        </div>
      </div>
    </div><!-- /cart-summary-col -->

  </div><!-- /cart-layout -->

  <!-- ── Trust Badges ── -->
  <div class="trust-badges">
    <div class="trust-badge">
      <span class="trust-badge-icon">🔒</span>
      <div class="trust-badge-text">
        <strong>Secure Payment</strong>
        <span>256-bit SSL encryption</span>
      </div>
    </div>
    <div class="trust-badge">
      <span class="trust-badge-icon">🌸</span>
      <div class="trust-badge-text">
        <strong>Satisfaction Guarantee</strong>
        <span>Freshness for 7 days</span>
      </div>
    </div>
    <div class="trust-badge">
      <span class="trust-badge-icon">🎧</span>
      <div class="trust-badge-text">
        <strong>Premium Support</strong>
        <span>24/7 Florist Assistance</span>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- ── Empty Cart State ── -->
  <div class="cart-page-wrapper">
    <div class="cart-empty">
      <h2>Your bag is empty</h2>
      <p>Discover our curated floral arrangements and find something beautiful.</p>
      <a href="collections.php">Explore Collections</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Footer ── -->
  <footer>
    <div class="footer-grid-layout">
      <div class="footer-brand-column">
        <h3>Petals & Co</h3>
        <p>Elevating the art of gifting through bespoke floral arrangements and premium experiences.</p>
        <div class="footer-social-links">
          <a href="#" title="Instagram"><svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" fill="none" stroke-width="2"></rect><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01" stroke="currentColor" fill="currentColor"></path></svg></a>
          <a href="#" title="Pinterest"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.24 2.65 7.86 6.39 9.29-.09-.78-.17-1.98.04-2.83.18-.77 1.22-5.17 1.22-5.17s-.31-.62-.31-1.54c0-1.45.84-2.53 1.88-2.53.89 0 1.32.67 1.32 1.47 0 .89-.57 2.23-.86 3.47-.25 1.03.52 1.87 1.53 1.87 1.83 0 3.24-1.93 3.24-4.72 0-2.47-1.77-4.2-4.31-4.2-2.94 0-4.66 2.2-4.66 4.48 0 .89.34 1.84.77 2.36.08.1.09.19.07.29-.08.31-.25 1.03-.28 1.18-.04.19-.14.23-.32.14-1.25-.58-2.03-2.42-2.03-3.89 0-3.15 2.29-6.04 6.6-6.04 3.46 0 6.15 2.47 6.15 5.77 0 3.45-2.17 6.22-5.19 6.22-1.01 0-1.97-.53-2.3-1.15l-.62 2.33c-.22.87-.83 1.95-1.24 2.61.93.29 1.92.44 2.94.44 5.52 0 10-4.48 10-10S17.52 2 12 2z" fill="currentColor"></path></svg></a>
        </div>
      </div>

      <div class="footer-links-column">
        <h4>SHOP</h4>
        <ul>
          <li><a href="collections.php">Best Sellers</a></li>
          <li><a href="collections.php">Collections</a></li>
          <li><a href="about.php">About Us</a></li>
        </ul>
      </div>

      <div class="footer-links-column">
        <h4>CUSTOMER CARE</h4>
        <ul>
          <li><a href="#">Shipping & Returns</a></li>
          <li><a href="#">Flower Care</a></li>
          <li><a href="about.php">Contact Us</a></li>
        </ul>
      </div>

      <div class="footer-links-column">
        <h4>CONNECT</h4>
        <ul>
          <li><a href="#">Instagram</a></li>
          <li><a href="#">Pinterest</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2024 Petals & Co. Crafted for elegance.</p>
    </div>
  </footer>

</body>
</html>
