-- ============================================================
-- DATABASE: florist
-- Sistem Penjualan Bunga (Admin & Customer)
-- ============================================================

-- 1️⃣ Buat database
CREATE DATABASE IF NOT EXISTS florist;
USE florist;

-- ============================================================
-- 2️⃣ Tabel Users (admin & customer)
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 3️⃣ Tabel Flowers (katalog bunga)
-- ============================================================
CREATE TABLE flowers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 4️⃣ Tabel Orders (pesanan pelanggan)
-- ============================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- 5️⃣ Tabel Order_Items (rincian produk dalam pesanan)
-- ============================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    flower_id INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (flower_id) REFERENCES flowers(id) ON DELETE CASCADE
);

-- ============================================================
-- 6️⃣ Tabel Payments (opsional: pembayaran pesanan)
-- ============================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('transfer', 'cod') DEFAULT 'transfer',
    payment_status ENUM('unpaid', 'paid', 'failed') DEFAULT 'unpaid',
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    proof_image VARCHAR(255),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ============================================================
-- 7️⃣ Tabel Cart (opsional: keranjang sementara)
-- ============================================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flower_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (flower_id) REFERENCES flowers(id) ON DELETE CASCADE
);

-- ============================================================
-- 8️⃣ Tambahkan akun admin awal
-- (password nanti di-hash lewat PHP)
-- ============================================================
INSERT INTO users (name, email, password, role)
VALUES ('Admin Florist', 'admin@florist.com', 'admin123', 'admin');

-- ============================================================
-- 9️⃣ Tambahkan contoh data bunga
-- ============================================================
INSERT INTO flowers (name, category, price, stock, description, image) VALUES
('Mawar Merah', 'Mawar', 25000, 30, 'Bunga mawar merah segar melambangkan cinta.', 'mawar_merah.jpg'),
('Tulip Putih', 'Tulip', 35000, 20, 'Tulip putih elegan dan sederhana.', 'tulip_putih.jpg'),
('Anggrek Ungu', 'Anggrek', 50000, 15, 'Anggrek ungu menawan untuk hadiah spesial.', 'anggrek_ungu.jpg');


