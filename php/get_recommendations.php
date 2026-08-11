<?php
include "connect.php";

$top_res = mysqli_query($conn, "
    SELECT p.id, p.name, p.price, p.image, SUM(oi.quantity) AS qty 
    FROM products p
    JOIN order_items oi ON p.name = oi.product
    GROUP BY p.id
    ORDER BY qty DESC
    LIMIT 3
");

$recommendations = [];
while ($row = $top_res ? mysqli_fetch_assoc($top_res) : null) {
    if ($row) {
        $recommendations[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => (int)$row['price'],
            'image' => $row['image']
        ];
    }
}

// Fallback to latest products if no sales data exists
if (count($recommendations) === 0) {
    $fallback_res = mysqli_query($conn, "SELECT id, name, price, image FROM products ORDER BY id DESC LIMIT 3");
    while ($row = $fallback_res ? mysqli_fetch_assoc($fallback_res) : null) {
        if ($row) {
            $recommendations[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'price' => (int)$row['price'],
                'image' => $row['image']
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($recommendations);
exit;
?>
