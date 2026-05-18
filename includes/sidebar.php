<div class="sidebar">
    <div class="sidebar-header">
        <h2>Bloommthis</h2>
        <div class="admin-profile">
            <img src="assets/img/profile.png" alt="Profile">
            <span><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="products.php">Produk</a></li>
        <li><a href="customers.php">Pelanggan</a></li>
        <li><a href="orders.php">Pesanan</a></li>
        <li><a href="reports.php">Laporan</a></li>
        <li><a href="profile.php">Akun</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
