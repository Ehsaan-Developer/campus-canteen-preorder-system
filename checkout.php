<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
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

    <h1>Checkout</h1>

    <form action="php/place_order.php" method="POST" onsubmit="return prepareOrderData();">
        <label>Your Name:</label><br>
        <input type="text" name="customer" required><br><br>

        <label>Pickup Time:</label><br>
        <input type="time" name="pickup_time" required><br><br>

        <!-- hidden fields -->
        <input type="hidden" name="cart_json" id="cart_json">
        <input type="hidden" name="total" id="total">

        <button type="submit" class="btn">Place Order</button>
    </form><br>


    <a class="btn  btn-danger" href="cart.html">Back to Cart</a>

    <script>
        function prepareOrderData() {
            const cart = JSON.parse(localStorage.getItem("cart")) || [];

            if (cart.length === 0) {
                alert("Cart is empty!");
                return false;
            }

            let total = 0;
            cart.forEach(i => total += Number(i.price) * Number(i.qty));

            document.getElementById("cart_json").value = JSON.stringify(cart);
            document.getElementById("total").value = total;

            return true;
        }
    </script>
</div>
<?php include 'footer.php'; ?>
</body>

</html>