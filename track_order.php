<?php
include "php/connect.php";

$order = null;
$error = "";

if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];

    $res = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $order = mysqli_fetch_assoc($res);
    } else {
        $error = "Order not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Track Order</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">

  <div class="navbar">
     <div class="nav-left">
                <a href="about.php" style="font-weight: bold; color: #007bff; border-right: 2px solid #ddd; padding-right: 15px; margin-right: 10px;">About</a>
    <a href="menu.php">Menu</a> |
    <a href="cart.html">Cart</a> |
    <a href="checkout.php">Checkout</a> |
    <a href="track_order.php">Track Order</a> |
    <a href="order_history.php">Order History</a> |
    <a href="analysis.php">Sales Analysis</a> |
    <a href="admin_orders.php">Admin Orders</a> |
    <a href="admin_products.php">Admin Products</a>
  </div>
  </div>

<h1>Track Your Order</h1>

<form method="GET" action="">
  <label>Enter Order ID:</label><br>
  <input type="number" name="order_id" required>
  <button type="submit" class="btn">Check Status</button>
</form>

<?php if ($error != "") { ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php } ?>

<?php if ($order) { ?>
  <h3>Order Details</h3>
  <p><b>Order ID:</b> <?php echo $order['order_id']; ?></p>
  <p><b>Name:</b> <?php echo htmlspecialchars($order['customer']); ?></p>
  <p><b>Pickup Time:</b> <?php echo htmlspecialchars($order['pickup_time']); ?></p>
  <p><b>Total:</b> Rs. <?php echo (int)$order['total']; ?></p>
  <p><b>Status:</b> <?php echo htmlspecialchars($order['status']); ?></p>
<?php } ?>

<p><a href="menu.php">Back to Menu</a></p>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
