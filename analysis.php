<?php
$path = __DIR__ . "/python/analysis.json";

if (!file_exists($path)) {
    die("analysis.json not found. Please run python/analysis.py first.");
}

$data = file_get_contents($path);
$analysis = json_decode($data, true);

if (!$analysis) {
    die("analysis.json is not valid JSON.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sales Analysis</title>
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


    <h1>Sales Analysis</h1>

    <p><b>Total Orders:</b> <?php echo (int)$analysis['total_orders']; ?></p>
    <p><b>Total Revenue:</b> Rs. <?php echo (int)$analysis['total_revenue']; ?></p>

    <h3>Top Items</h3>
    <ul>
        <?php foreach ($analysis['top_items'] as $item) { ?>
            <li><?php echo htmlspecialchars($item['product']); ?> (Qty: <?php echo (int)$item['qty']; ?>)</li>
        <?php } ?>
    </ul>
    <h3>Top Combos (Recommendations)</h3>
<ul>
  <?php if (!empty($analysis['top_combos'])) { ?>
    <?php foreach($analysis['top_combos'] as $c) { ?>
      <li>
        <?php echo htmlspecialchars($c['item1']); ?> + <?php echo htmlspecialchars($c['item2']); ?>
        (Together: <?php echo (int)$c['count']; ?> times)
      </li>
    <?php } ?>
  <?php } else { ?>
    <li>No combo data yet (place more multi-item orders).</li>
  <?php } ?>
</ul>


    <p><a href="menu.php">Back to Menu</a></p>
</div>
<?php include 'footer.php'; ?>
</body>

</html>