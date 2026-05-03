<?php
include "connect.php";

if (!isset($conn) || !$conn) {
  die("Database connection not available");
}

$order_id = (int)($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? 'Pending';

$allowed = ['Pending','Preparing','Ready'];
if (!in_array($status, $allowed)) {
  die("Invalid status");
}

if ($order_id > 0) {
  mysqli_query($conn, "UPDATE orders SET status='$status' WHERE order_id=$order_id");
}

header("Location: ../admin_orders.php");
exit;
?>
