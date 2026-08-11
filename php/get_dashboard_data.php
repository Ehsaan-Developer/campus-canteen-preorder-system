<?php
include "connect.php";
include "auth.php";

require_admin();

// 1. Fetch live metrics
$rev_res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM orders");
$rev_row = mysqli_fetch_assoc($rev_res);
$total_revenue = (int)($rev_row['revenue'] ?? 0);

$active_res = mysqli_query($conn, "SELECT COUNT(*) as active FROM orders WHERE status IN ('Pending', 'Preparing')");
$active_row = mysqli_fetch_assoc($active_res);
$active_orders = (int)($active_row['active'] ?? 0);

$ready_res = mysqli_query($conn, "SELECT COUNT(*) as ready FROM orders WHERE status = 'Ready'");
$ready_row = mysqli_fetch_assoc($ready_res);
$ready_orders = (int)($ready_row['ready'] ?? 0);

$prod_res = mysqli_query($conn, "SELECT COUNT(*) as prods FROM products");
$prod_row = mysqli_fetch_assoc($prod_res);
$total_products = (int)($prod_row['prods'] ?? 0);

// 2. Fetch pending/preparing orders
$orders_query = "
    SELECT o.*, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.product) SEPARATOR ', ') as items_list
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.status IN ('Pending', 'Preparing')
    GROUP BY o.order_id
    ORDER BY o.order_id ASC
";
$orders_result = mysqli_query($conn, $orders_query);
$orders = [];
while ($row = $orders_result ? mysqli_fetch_assoc($orders_result) : null) {
    if ($row) {
        $orders[] = [
            'order_id' => (int)$row['order_id'],
            'customer' => $row['customer'],
            'pickup_time' => $row['pickup_time'],
            'items_list' => $row['items_list'] ?? 'No items details',
            'total' => (int)$row['total'],
            'status' => $row['status']
        ];
    }
}

$response = [
    'metrics' => [
        'total_revenue' => $total_revenue,
        'active_orders' => $active_orders,
        'ready_orders' => $ready_orders,
        'total_products' => $total_products
    ],
    'orders' => $orders
];

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
