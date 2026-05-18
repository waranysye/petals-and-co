<?php
  // Ambil nama file yang sedang dibuka
  $current_page = basename($_SERVER['PHP_SELF']);
  session_start();
include '../Config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <!-- Panggil CSS -->
 <link rel="stylesheet" href="../Assets/css/indexcostumer.css">
</head>
<body>

<header>
  <!-- Kiri: Account -->
  <div class="header-left">
    <a href="<?php echo isset($_SESSION['user_id']) ? 'profilecst.php' : '../auth/login.php'; ?>" 
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


   <!-- Banner Promo -->
  <section class="promo-banner">
     <img src="../Assets/img/promosi.png" alt="Chart" width="20"> Chart
    <div class="promo-text">
      <a href="viewproduct.php" class="btn-shop">Shop Now</a>
    </div>
  </section>

  <!-- Shop by Category -->
  <section class="category">
    <h2>Shop by Category</h2>
    <div class="category-cards">
      <a href="viewmatahari.php" class="card">   
     <img src="../Assets/img/sunflowerctg.jpg" alt="Chart" width="20"> 
        <h3>Bunga Matahari</h3>
      </a>
      <a href="viewmawar.php" class="card">
        <img src="../Assets/img/mawarctg.jpg" alt="Bunga Mawar">
        <h3>Bunga Mawar</h3>
      </a>
      <a href="viewanggrek.php" class="card">
        <img src="../Assets/img/anggrekctg.jpg" alt="Bunga Anggrek">
        <h3>Bunga Anggrek</h3>
      </a>
    </div>
  </section>

<!-- New Arrivals -->
<section class="new-arrivals">
  <h2>New Arrivals</h2>
  <div class="product-cards">
    <?php
      // Ambil 3 produk terbaru
      $result = $conn->query("SELECT * FROM flowers ORDER BY created_at DESC LIMIT 3");

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo '
            <div class="product-card">
              <img src="../Assets/img/'.$row['image'].'" alt="'.$row['name'].'">
              <h3>'.$row['name'].'</h3>
              <p class="desc">'.$row['description'].'</p>
              <p class="price">Rp '.number_format($row['price'], 3, ".", ".").'</p>

              <div class="actions">
                <button class="btn minus" data-id="'.$row['id'].'" data-action="remove">-</button>
                <button class="btn plus" data-id="'.$row['id'].'" data-action="add">+</button>
              </div>
            </div>
          ';
        }
      } else {
        echo "<p>Belum ada produk tersedia.</p>";
      }
    ?>
  </div>
</section>

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
        let badge = document.querySelector('.cart-badge');
        if (!badge) {
          const cartLink = document.querySelector('.header-right a[href="cart.php"]');
          badge = document.createElement('span');
          badge.className = 'cart-badge';
          cartLink.appendChild(badge);
        }
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
