<?php
include "php/connect.php";

$customer = trim($_GET['customer'] ?? '');
$orders = [];

if ($customer !== '') {
    $safe = mysqli_real_escape_string($conn, $customer);
    $res = mysqli_query($conn, "SELECT * FROM orders WHERE customer='$safe' ORDER BY order_id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Order History</title>
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


<h1>Your Order History</h1>

<form method="GET">
  <label>Enter your name:</label><br>
  <input type="text" name="customer" value="<?php echo htmlspecialchars($customer); ?>" required>
  <button type="submit">Show History</button>
</form>

<?php if ($customer !== '' && count($orders) === 0) { ?>
  <p>No orders found for "<?php echo htmlspecialchars($customer); ?>"</p>
<?php } ?>

<?php if (count($orders) > 0) { ?>
  <h3>Orders for: <?php echo htmlspecialchars($customer); ?></h3>
  <table border="1" cellpadding="10">
    <tr>
      <th>Order ID</th>
      <th>Pickup Time</th>
      <th>Total</th>
      <th>Status</th>
      <th>Details</th>
    </tr>
    <?php foreach($orders as $o) { ?>
      <tr>
        <td><?php echo (int)$o['order_id']; ?></td>
        <td><?php echo htmlspecialchars($o['pickup_time']); ?></td>
        <td>Rs. <?php echo (int)$o['total']; ?></td>
        <td><?php echo htmlspecialchars($o['status']); ?></td>
        <td><a href="track_order.php?order_id=<?php echo (int)$o['order_id']; ?>">View</a></td>
      </tr>
    <?php } ?>
  </table>
<?php } ?>

<p><a href="menu.php">Back to Menu</a></p>
</div>
<?php include 'footer.php'; ?>

</body>
</html>
