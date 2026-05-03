<!DOCTYPE html>
<html>

<head>
  <title>Add Product</title>
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

    <h1>Add Product</h1>

    <form action="php/add_product.php" method="POST">
      <label>Name:</label><br>
      <input type="text" name="name" required><br><br>

      <label>Price:</label><br>
      <input type="number" name="price" required><br><br>

      <button type="submit" class="btn">Add</button><br><br>
    </form>

    <a class="btn btn-danger" href="admin_products.php">Back</a>
  </div>
  <?php include 'footer.php'; ?>
</body>

</html>