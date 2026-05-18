<?php
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
  include '../Config/database.php';

  // Ambil jumlah item di keranjang secara dinamis saat load pertama
  $cartCount = 0;
  if (isset($_SESSION['user_id'])) {
      $userId = $_SESSION['user_id'];
      $stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
      $stmt->bind_param("i", $userId);
      $stmt->execute();
      $res = $stmt->get_result()->fetch_assoc();
      $cartCount = $res['total_items'] ? $res['total_items'] : 0;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | Petals & Co</title>
  <!-- Panggil CSS -->
  <link rel="stylesheet" href="../Assets/css/indexcostumer.css?v=<?php echo time(); ?>">
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

  <!-- About Hero -->
  <div class="about-hero-block">
    <h1>Our Story</h1>
    <p>The Art of Premium Floral Arrangements — Expressing Every Feeling.</p>
  </div>

  <!-- About Story Section -->
  <section class="about-story-section">
    <h2>Crafted with Love in Jakarta</h2>
    <p>Petals & Co was born from a deep passion for botanical beauty and the desire to bring joy into everyday life. We believe that flowers are one of the most honest forms of natural expression — capable of conveying love, sympathy, joy, and appreciation without a single word.</p>
    
    <p>Based in Kemang, South Jakarta, our flower boutique crafts every floral arrangement personally with the highest artistic precision. Our blooms are curated directly from the finest local and international flower farms each morning to ensure peak freshness and captivating beauty.</p>

    <div style="margin: 40px 0; border-top: 1px solid var(--border-light); padding-top: 40px;">
      <h3 style="font-family: var(--font-serif); font-size: 24px; color: var(--primary-green); margin-bottom: 20px; text-align: center;">Our Promises</h3>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 20px;">
        <div style="background-color: var(--bg-cream); padding: 25px; border-radius: 4px; text-align: center;">
          <h4 style="font-family: var(--font-serif); font-size: 18px; color: var(--primary-green); margin-bottom: 10px;">Fresh Daily Guarantee</h4>
          <p style="font-size: 14px; color: var(--text-muted);">Only the freshest, highest-quality blooms pass our curators' rigorous selection before being arranged.</p>
        </div>
        
        <div style="background-color: var(--bg-cream); padding: 25px; border-radius: 4px; text-align: center;">
          <h4 style="font-family: var(--font-serif); font-size: 18px; color: var(--primary-green); margin-bottom: 10px;">Sameday Swift Delivery</h4>
          <p style="font-size: 14px; color: var(--text-muted);">Our independent logistics system guarantees swift 2-hour delivery to preserve the beauty of every bloom.</p>
        </div>

        <div style="background-color: var(--bg-cream); padding: 25px; border-radius: 4px; text-align: center;">
          <h4 style="font-family: var(--font-serif); font-size: 18px; color: var(--primary-green); margin-bottom: 10px;">Bespoke Artistry</h4>
          <p style="font-size: 14px; color: var(--text-muted);">Every arrangement is personally designed by licensed florists with a modern premium aesthetic vision.</p>
        </div>
      </div>
    </div>

    <div style="margin-top: 60px; text-align: center;">
      <h3 style="font-family: var(--font-serif); font-size: 24px; color: var(--primary-green); margin-bottom: 15px;">Get In Touch</h3>
      <p style="color: var(--text-muted); margin-bottom: 30px;">Need a custom arrangement consultation or special event decor? Our customer support team is ready to assist you.</p>
      
      <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <a href="mailto:hello@petalsco.co.id" class="btn-primary" style="display: inline-block; padding: 12px 30px;">Email Us</a>
        <a href="https://wa.me/62215555888" target="_blank" class="btn-secondary" style="display: inline-block; padding: 12px 30px; border-color: var(--primary-green); color: var(--primary-green);">WhatsApp Support</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="footer-grid-layout">
      <div class="footer-brand-column">
        <h3>Petals & Co</h3>
        <p>Membawa keindahan alam ke dalam setiap momen berharga dalam hidup Anda melalui seni rangkaian bunga.</p>
        <div class="footer-social-links">
          <a href="#" title="Website"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" stroke-width="2"></circle><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" fill="none" stroke-width="2"></path></svg></a>
          <a href="#" title="Instagram"><svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" fill="none" stroke-width="2"></rect><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01" stroke="currentColor" fill="currentColor"></path></svg></a>
          <a href="#" title="Facebook"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" fill="currentColor"></path></svg></a>
        </div>
      </div>

      <div class="footer-links-column">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="collections.php">Collections</a></li>
          <li><a href="idexcostumer.php#category-section">New Arrivals</a></li>
          <li><a href="about.php">Flower Care</a></li>
        </ul>
      </div>

      <div class="footer-links-column">
        <h4>Support</h4>
        <ul>
          <li><a href="about.php">Contact Us</a></li>
          <li><a href="#">Shipping & Returns</a></li>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
        </ul>
      </div>

      <div class="footer-links-column">
        <h4>Get In Touch</h4>
        <ul class="footer-contact-info-list">
          <li>Jl. Kemang Raya No. 151,<br>Jakarta Selatan, 12730</li>
          <li>+62 21 5555 888</li>
          <li>hello@petalsco.co.id</li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom-copyright">
      <p>&copy; 2026 Petals & Co. Crafted with love.</p>
      <p>All Rights Reserved.</p>
    </div>
  </footer>

</body>
</html>
