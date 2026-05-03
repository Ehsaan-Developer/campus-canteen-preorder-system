<?php
include "connect.php";

if (!isset($conn)) {
  if (isset($con)) {
    $conn = $con;
  } elseif (isset($connection)) {
    $conn = $connection;
  } elseif (isset($db)) {
    $conn = $db;
  } else {
    die("Database connection not available.");
  }
}

$customer = $_POST['customer'] ?? '';
$pickup_time = $_POST['pickup_time'] ?? '';
$total = $_POST['total'] ?? 0;
$cart_json = $_POST['cart_json'] ?? '';

if ($customer === '' || $pickup_time === '' || $cart_json === '') {
  die("Missing data!");
}

$cart = json_decode($cart_json, true);
if (!is_array($cart) || count($cart) === 0) {
  die("Cart invalid or empty!");
}

/* 1) Insert into orders */
$stmt = mysqli_prepare($conn, "INSERT INTO orders (customer, pickup_time, total, status) VALUES (?, ?, ?, 'Pending')");
mysqli_stmt_bind_param($stmt, "ssi", $customer, $pickup_time, $total);
mysqli_stmt_execute($stmt);

$order_id = mysqli_insert_id($conn);

/* 2) Insert into order_items */
$stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product, quantity) VALUES (?, ?, ?)");

foreach ($cart as $item) {
  $product = $item['name'];
  $qty = (int)($item['qty'] ?? 1);

  mysqli_stmt_bind_param($stmt2, "isi", $order_id, $product, $qty);
  mysqli_stmt_execute($stmt2);
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Order Success</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <div class="container">

    <div class="navbar">
      <a href="../menu.php">Menu</a> |
      <a href="../cart.html">Cart</a> |
      <a href="../checkout.php">Checkout</a> |
      <a href="../track_order.php">Track Order</a> |
      <a href="../order_history.php">Order History</a> |
      <a href="../analysis.php">Sales Analysis</a> |
      <a href="../admin_orders.php">Admin Orders</a> |
      <a href="../admin_products.php">Admin Products</a>
    </div>

    <h2>Order Placed Successfully!</h2>

    <p>
      <a class="btn" href="../track_order.php?order_id=<?php echo (int)$order_id; ?>">
        Track this order
      </a>
    </p>

    <p><b>Order ID:</b> <?php echo (int)$order_id; ?></p>
    <p><b>Name:</b> <?php echo htmlspecialchars($customer); ?></p>
    <p><b>Pickup Time:</b> <?php echo htmlspecialchars($pickup_time); ?></p>
    <p><b>Total:</b> Rs. <?php echo (int)$total; ?></p>

    <p>
      <a class="btn" href="../menu.php">Back to Menu</a>
    </p>

  </div>

  <script>
    localStorage.removeItem('cart');
  </script>
  <footer style="text-align: center; padding: 20px; background-color: #f1f1f1; margin-top: 50px; border-top: 1px solid #ddd;">
    <p style="margin: 0; font-family: sans-serif; color: #333;">

      Designed & Developed by <strong>Ehsaan Ul Haq Tawakly</strong>
      <img src="https://flagcdn.com/w40/pk.png" width="30" alt="Pakistan Flag">
    </p>
    <p style="margin: 5px 0 0; font-size: 14px; color: #666;">
      &copy; 2026 Campus Canteen System | All Rights Reserved
    </p>
  </footer>
</body>

</html>