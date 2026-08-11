<?php
include "connect.php";

$order_id = (int)($_GET['order_id'] ?? 0);

$response = ['success' => false, 'status' => 'Pending'];

if ($order_id > 0) {
    $res = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = $order_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $order = mysqli_fetch_assoc($res);
        $response['success'] = true;
        $response['status'] = $order['status'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
