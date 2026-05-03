<?php
include(__DIR__ . "/php/connect.php");

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("DB Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Canteen Menu</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/cart.js"></script>
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

            <a class="cart-btn" href="cart.html">Go to Cart</a>
        </div>


        <h1 class="page-title">Campus Canteen Menu</h1>

        <?php
        echo "<div class='menu-grid'>";

        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'] ?? '';
            $price = (int)($row['price'] ?? 0);

            $safeName = addslashes($name);

            echo "<div class='menu-card'>";
            echo "<h3>" . htmlspecialchars($name) . "</h3>";
            echo "<div class='price'>Rs. " . $price . "</div>";
            echo "<button class='btn' onclick=\"addToCart('{$safeName}', {$price})\">Add to Cart</button>";
            echo "</div>";
        }

        echo "</div>";
        ?>

    </div>

    <?php include 'footer.php'; ?>

</body>

</html>