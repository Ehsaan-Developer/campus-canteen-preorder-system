<?php
include "php/connect.php";
include_once "php/auth.php";

$orders = [];
$order_ids_str = trim($_GET['order_ids'] ?? '');

if ($order_ids_str !== '') {
    $parts = explode(',', $order_ids_str);
    $clean_ids = [];
    foreach ($parts as $p) {
        $id = (int)$p;
        if ($id > 0) {
            $clean_ids[] = $id;
        }
    }
    
    if (count($clean_ids) > 0) {
        $ids_list = implode(',', $clean_ids);
        $res = mysqli_query($conn, "SELECT * FROM orders WHERE order_id IN ($ids_list) ORDER BY order_id DESC");
        while ($row = $res ? mysqli_fetch_assoc($res) : null) {
            if ($row) $orders[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History | Campus Canteen</title>
  <link rel="manifest" href="manifest.json">
  <!-- FontAwesome for professional icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/cart.js"></script>
  <style>
    .live-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: var(--accent-success);
        border-radius: 50%;
        margin-right: 2px;
        animation: pulse-live 1.5s infinite;
    }
    @keyframes pulse-live {
        0% { transform: scale(0.9); opacity: 0.5; }
        50% { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.5; }
    }
    .history-box {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow-md);
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Customer Navbar -->
    <div class="navbar">
      <div class="nav-left">
        <a href="about.php" style="font-weight: bold; color: var(--primary); border-right: 2px solid var(--border-color); padding-right: 15px; margin-right: 10px;"><i class="fa-solid fa-circle-info"></i> About</a>
        <a href="menu.php"><i class="fa-solid fa-utensils"></i> Menu</a> |
        <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a> |
        <a href="checkout.php"><i class="fa-solid fa-credit-card"></i> Checkout</a> |
        <a href="track_order.php"><i class="fa-solid fa-truck-ramp-box"></i> Track Order</a> |
        <a href="order_history.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
      </div>
      <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        <span style="font-size: 0.85rem; color: var(--accent-success); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.08); padding: 5px 12px; border-radius: 50px;">
            <span class="live-dot"></span>
            Live Connected
        </span>
        <a href="login.php" style="font-size: 0.9rem; color: var(--text-muted); text-decoration: none; font-weight: 600; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: #fff; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-muted)';">
            <i class="fa-solid fa-user-shield"></i> Admin Portal
        </a>
      </div>
    </div>

    <h1 class="page-title">Your Order History</h1>

    <div class="history-box">
      <?php if (count($orders) > 0) { ?>
        <h3 style="font-family: Outfit; margin-bottom: 20px; color: var(--text-main);">Showing past orders placed on this device:</h3>
        <table border="0">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Pickup Time</th>
              <th>Total Bill</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($orders as $o) { 
              $status = $o['status'];
              $badgeClass = '';
              if ($status === 'Pending') $badgeClass = 'badge-pending';
              elseif ($status === 'Preparing') $badgeClass = 'badge-preparing';
              elseif ($status === 'Ready') $badgeClass = 'badge-ready';
              ?>
              <tr>
                <td><strong>#<?php echo (int)$o['order_id']; ?></strong></td>
                <td><?php echo htmlspecialchars($o['customer']); ?></td>
                <td><?php echo htmlspecialchars($o['pickup_time']); ?></td>
                <td>Rs. <?php echo (int)$o['total']; ?></td>
                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <a class="btn" style="padding: 6px 12px; font-size: 0.85rem;" href="track_order.php?order_id=<?php echo (int)$o['order_id']; ?>">Track Live</a>
                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem;" onclick="reorder(<?php echo (int)$o['order_id']; ?>)">Reorder</button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
          <p style="font-size: 1.1rem; margin-bottom: 10px;">No recent orders detected on this device.</p>
          <span style="font-size: 0.9rem;">Once you place an order, it will appear here automatically.</span>
        </div>
      <?php } ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has("order_ids")) {
      const localIds = JSON.parse(localStorage.getItem("canteen_order_ids")) || [];
      if (localIds.length > 0) {
        window.location.href = "order_history.php?order_ids=" + localIds.join(",");
      }
    }

    function reorder(orderId) {
      if (confirm(`Do you want to re-order all items from Order #${orderId}?`)) {
        fetch(`php/get_order_items.php?order_id=${orderId}`)
          .then(res => res.json())
          .then(items => {
            if (items.length === 0) {
              alert("Could not retrieve items from that order.");
              return;
            }
            
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            
            items.forEach(item => {
              const existing = cart.find(i => i.name === item.name);
              if (existing) {
                existing.qty += item.qty;
              } else {
                cart.push({
                  name: item.name,
                  price: Number(item.price),
                  qty: Number(item.qty)
                });
              }
            });
            
            localStorage.setItem("cart", JSON.stringify(cart));
            alert("Items added back to your cart! Redirecting to menu...");
            window.location.href = "menu.php";
          })
          .catch(err => {
            console.error("Reorder failed:", err);
            alert("Error loading order items. Please try again.");
          });
      }
    }
  </script>
</body>
</html>
