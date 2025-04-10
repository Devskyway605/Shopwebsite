<?php
header('Content-Type: application/json');
require_once 'conn.php'; // 連接資料庫

$range = $_GET['range'] ?? 'all'; // 取得時間參數
$where = '1'; // 預設不篩選

switch ($range) {
    case 'today':
        $where = "DATE(o.order_time) = CURDATE()";
        break;
    case 'week':
        $where = "YEARWEEK(o.order_time, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case '3months':
        $where = "o.order_time >= CURDATE() - INTERVAL 3 MONTH";
        break;
}

$sql = "SELECT p.Name AS product_name, SUM(oi.quantity) AS total_quantity
  FROM orders o
  JOIN order_items oi ON o.order_id = oi.order_id
  JOIN Products p ON oi.product_id = p.Product_id
  WHERE $where
  GROUP BY p.Name
  ORDER BY total_quantity DESC;";

$result = $pdo->query($sql);
$data = ['labels' => [], 'values' => []];

while ($row = $result->fetch()) {
    $data['labels'][] = $row['product_name'];
    $data['values'][] = (int)$row['total_quantity'];
}

echo json_encode($data);
