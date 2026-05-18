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

  // Tangkap query pencarian & kategori
  $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
  $category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, trim($_GET['category'])) : '';

  // Buat query SQL dinamis
  $sql = "SELECT * FROM flowers WHERE 1=1";
  if ($search !== '') {
      $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%')";
  }
  if ($category_filter !== '') {
      // Map kategori
      $cat_map = [
          'mawar' => 'Bunga Mawar',
          'matahari' => 'Bunga Matahari',
          'anggrek' => 'Bunga Anggrek'
      ];
      if (array_key_exists($category_filter, $cat_map)) {
          $db_category = $cat_map[$category_filter];
          $sql .= " AND category = '$db_category'";
      }
  }
  $sql .= " ORDER BY name ASC";
  $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Collections | Petals & Co</title>
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
      <a href="collections.php" class="<?php echo ($current_page == 'collections.php' && $category_filter == '') ? 'active' : ''; ?>">Collections</a>
      <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
    </nav>

    <div class="header-right">
      <!-- Search Form -->
      <div class="search-container">
        <form action="collections.php" method="GET" class="search-form">
          <input type="text" name="search" placeholder="Search flowers..." value="<?php echo htmlspecialchars($search); ?>">
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

  <!-- Page title header -->
  <div class="page-header">
    <h1>Our Collections</h1>
    <p>Explore our curated collection of fresh floral arrangements, crafted specially to celebrate every emotion and precious moment.</p>
    <?php if ($search !== ''): ?>
      <div style="margin-top: 15px; font-size: 14px; font-weight: 600; color: var(--accent-pink);">
        Showing results for: "<?php echo htmlspecialchars($search); ?>" 
        <a href="collections.php" style="margin-left: 10px; font-weight: normal; font-size: 12px; text-decoration: underline; color: var(--text-muted);">Clear Search</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Layout Utama Halaman Koleksi -->
  <div class="collections-layout">
    
    <!-- Sidebar Kategori -->
    <aside class="collections-sidebar">
      <h3 class="sidebar-title">Categories</h3>
      <ul class="category-filter-list">
        <li><a href="collections.php" class="<?php echo ($category_filter == '') ? 'active' : ''; ?>">All Collections</a></li>
        <li><a href="collections.php?category=mawar" class="<?php echo ($category_filter == 'mawar') ? 'active' : ''; ?>">Roses</a></li>
        <li><a href="collections.php?category=matahari" class="<?php echo ($category_filter == 'matahari') ? 'active' : ''; ?>">Sunflowers</a></li>
        <li><a href="collections.php?category=anggrek" class="<?php echo ($category_filter == 'anggrek') ? 'active' : ''; ?>">Orchids</a></li>
      </ul>
    </aside>

    <!-- Catalog Grid -->
    <main class="collections-catalog-wrapper">
      <div class="collections-catalog-grid">
        <?php
          if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              $badge = '';
              if ($row['category'] == 'Bunga Mawar') {
                  $badge = '<span class="product-badge-accent best-seller">Best Seller</span>';
              } else if ($row['category'] == 'Bunga Anggrek') {
                  $badge = '<span class="product-badge-accent seasonal">Seasonal</span>';
              } else {
                  $badge = '<span class="product-badge-accent best-seller" style="background-color: var(--primary-green);">Featured</span>';
              }

              echo '
                <div class="product-card-premium">
                  <div class="product-card-img-wrapper">
                    '.$badge.'
                    <img src="../Assets/img/'.$row['image'].'" alt="'.$row['name'].'">
                  </div>
                  <div class="product-info-details">
                    <div class="product-title-row">
                      <h3>'.$row['name'].'</h3>
                      <button class="btn-bookmark-heart" title="Add to Wishlist">
                        <svg viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke="currentColor" stroke-width="1.5" fill="none"></path></svg>
                      </button>
                    </div>
                    <div class="product-price-tag">Rp '.number_format($row['price'], 3, ".", ".").'</div>
                    <p class="product-description-text">'.$row['description'].'</p>

                    <div class="product-action-row">
                      <button class="btn-adjust minus" data-id="'.$row['id'].'" data-action="remove">-</button>
                      <span style="font-weight: 500; font-size: 13px; color: var(--text-dark); margin: 0 10px;">Cart Action</span>
                      <button class="btn-adjust plus" data-id="'.$row['id'].'" data-action="add">+</button>
                    </div>
                  </div>
                </div>
              ';
            }
          } else {
            echo "<p class='no-product'>No flowers found matching your search. <a href='collections.php'>Browse all</a></p>";
          }
        ?>
      </div>
    </main>

  </div>

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

  <!-- AJAX Cart Javascript -->
  <script>
  document.querySelectorAll('.product-action-row .btn-adjust').forEach(button => {
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
          let badge = document.querySelector('.cart-badge');
          if (!badge) {
            const cartLink = document.querySelector('.cart-icon-link');
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            cartLink.appendChild(badge);
          }
          badge.style.display = data.cart_count > 0 ? '' : 'none';
          badge.textContent = data.cart_count > 0 ? data.cart_count : '';
        } else {
          alert('Silakan login terlebih dahulu.');
        }
      })
      .catch(err => console.error(err));
    });
  });
  </script>

</body>
</html>
