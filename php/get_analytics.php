<?php
include "connect.php";
include "auth.php";

require_admin();

// 1. Total Orders & Revenue
$metrics_res = mysqli_query($conn, "SELECT COUNT(*) AS total_orders, COALESCE(SUM(total), 0) AS total_revenue FROM orders");
$metrics = mysqli_fetch_assoc($metrics_res);

// 2. Top Selling Items
$top_items = [];
$top_res = mysqli_query($conn, "SELECT product, SUM(quantity) AS qty FROM order_items GROUP BY product ORDER BY qty DESC LIMIT 5");
while ($row = $top_res ? mysqli_fetch_assoc($top_res) : null) {
    if ($row) {
        $top_items[] = [
            'product' => $row['product'],
            'qty' => (int)$row['qty']
        ];
    }
}

// 3. Smart Combo Recommendations (Frequent Itemsets via Association Rules in SQL)
$top_combos = [];
$combo_res = mysqli_query($conn, "
    SELECT a.product AS item1, b.product AS item2, COUNT(*) AS count
    FROM order_items a
    JOIN order_items b ON a.order_id = b.order_id AND a.product < b.product
    GROUP BY a.product, b.product
    ORDER BY count DESC
    LIMIT 3
");
while ($row = $combo_res ? mysqli_fetch_assoc($combo_res) : null) {
    if ($row) {
        $top_combos[] = [
            'item1' => $row['item1'],
            'item2' => $row['item2'],
            'count' => (int)$row['count']
        ];
    }
}

$response = [
    'total_orders' => (int)$metrics['total_orders'],
    'total_revenue' => (int)$metrics['total_revenue'],
    'top_items' => $top_items,
    'top_combos' => $top_combos
];

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
