<?php
header("Content-Type: application/json");
include("conn.php"); // 請換成你的資料庫連線

$member_id = $_GET['member_id'] ?? null;
if (!$member_id) {
  echo json_encode([]);
  exit;
}

// 查詢此會員所有訂單
$sql = "SELECT * FROM orders WHERE member_id = ? ORDER BY order_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$member_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 每張訂單加上對應的商品清單
foreach ($orders as &$order) {
  $sql = "SELECT oi.quantity, oi.price, p.Name as name
            FROM order_items oi
            JOIN Products p ON oi.product_id = p.Product_id
            WHERE oi.order_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$order['order_id']]);
  $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($orders);
