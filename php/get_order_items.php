<?php
include "connect.php";

$order_id = (int)($_GET['order_id'] ?? 0);
$items = [];

if ($order_id > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT oi.product AS name, oi.quantity AS qty, COALESCE(p.price, 0) AS price 
        FROM order_items oi
        LEFT JOIN products p ON oi.product = p.name
        WHERE oi.order_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = [
            'name' => $row['name'],
            'qty' => (int)$row['qty'],
            'price' => (int)$row['price']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($items);
exit;
?>
