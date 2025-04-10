<?php
include("conn.php");

$data = json_decode(file_get_contents("php://input"), true);
$cart = $data['cart'] ?? [];
$member_id = $data['member_id'] ?? null;
$member_username = $data['member_username'] ?? '';


// 新增訂單
$now = date("Y-m-d H:i:s");
$sql = "INSERT INTO orders (member_id, member_username, order_time) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$member_id, $member_username, $now]);
$order_id = $pdo-> lastInsertId();

// 新增訂單明細
foreach ($cart as $item) {
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $order_id,
        $item['Product_id'],
        $item['quantity'],
        $item['Price']
    ]);
}

echo json_encode(['status' => 'success']);
