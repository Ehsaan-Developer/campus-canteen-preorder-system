<?php
include "php/connect.php";

$id = (int)($_GET['id'] ?? 0);

$res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($res);

if (!$product) {
  die("Product not found");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
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

<h1>Edit Product</h1>

<form action="php/edit_product.php" method="POST">
  <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">

  <label>Name:</label><br>
  <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

  <label>Price:</label><br>
  <input type="number" name="price" value="<?php echo (int)$product['price']; ?>" required><br><br>

  <button type="submit" class="btn">Save</button><br><br>
</form>

<a class="btn btn-danger" href="admin_products.php">Back</a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
