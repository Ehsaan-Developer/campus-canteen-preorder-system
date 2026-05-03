<?php
include "php/connect.php";
$res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Products</title>
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


<h1>Admin - Products</h1>

<p><a class="btn" href="admin_add_product.php">+ Add New Product</a></p>

<table border="1" cellpadding="10">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Actions</th>
  </tr>

  <?php while($p = mysqli_fetch_assoc($res)) { ?>
    <tr>
      <td><?php echo (int)$p['id']; ?></td>
      <td><?php echo htmlspecialchars($p['name']); ?></td>
      <td>Rs. <?php echo (int)$p['price']; ?></td>
      <td>
        <a href="admin_edit_product.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
        |
        <a href="php/delete_product.php?id=<?php echo (int)$p['id']; ?>"
           onclick="return confirm('Delete this product?');">Delete</a>
      </td>
    </tr>
  <?php } ?>
</table>

<p><a href="admin_orders.php">Go to Admin Orders</a></p>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
