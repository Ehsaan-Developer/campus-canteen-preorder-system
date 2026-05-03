<?php
include "php/connect.php";
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_id DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin - Orders</title>
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

    <h1>Admin Orders</h1>

    <table border="1" cellpadding="10">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Pickup Time</th>
            <th>Total</th>
            <th>Status</th>
            <th>Change Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                <td><?php echo htmlspecialchars($row['pickup_time']); ?></td>
                <td>Rs. <?php echo (int)$row['total']; ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>

                <td>
                    <form action="php/update_status.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">

                        <select name="status">
                            <option value="Pending">Pending</option>
                            <option value="Preparing">Preparing</option>
                            <option value="Ready">Ready</option>
                        </select>

                        <button type="submit" class="btn">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
<?php include 'footer.php'; ?>
</body>

</html>