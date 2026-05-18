<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'User not logged in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$flower_id = intval($_POST['flower_id']);
$action = $_POST['action'];

// Cek apakah item sudah ada di cart
$check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND flower_id=?");
$check->bind_param("ii", $user_id, $flower_id);
$check->execute();
$res = $check->get_result();
$item = $res->fetch_assoc();

if ($action == 'add') {
  if ($item) {
    $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND flower_id=$flower_id");
  } else {
    $conn->query("INSERT INTO cart (user_id, flower_id, quantity) VALUES ($user_id, $flower_id, 1)");
  }
} elseif ($action == 'remove') {
  if ($item && $item['quantity'] > 1) {
    $conn->query("UPDATE cart SET quantity = quantity - 1 WHERE user_id=$user_id AND flower_id=$flower_id");
  } else {
    $conn->query("DELETE FROM cart WHERE user_id=$user_id AND flower_id=$flower_id");
  }
}

// Hitung ulang jumlah jenis produk di cart
$countRes = $conn->query("SELECT COUNT(*) as total FROM cart WHERE user_id=$user_id");
$countRow = $countRes->fetch_assoc();
$totalItems = $countRow['total'];

echo json_encode(['success' => true, 'total' => $totalItems]);
?>
