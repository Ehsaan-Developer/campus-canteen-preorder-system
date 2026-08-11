<?php
include_once(__DIR__ . "/php/auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart | Campus Canteen</title>
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
    .cart-container {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow-md);
      margin-top: 20px;
    }
    .cart-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid var(--border-color);
    }
    .cart-row:last-child {
      border-bottom: none;
    }
    .qty-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: 1px solid var(--border-color);
      background: #fff;
      font-weight: bold;
      cursor: pointer;
      font-size: 1.1rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .qty-btn:hover {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
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
        <a href="cart.php" class="active"><i class="fa-solid fa-cart-shopping"></i> Cart</a> |
        <a href="checkout.php"><i class="fa-solid fa-credit-card"></i> Checkout</a> |
        <a href="track_order.php"><i class="fa-solid fa-truck-ramp-box"></i> Track Order</a> |
        <a href="order_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
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

    <h1 class="page-title">Your Shopping Cart</h1>

    <div class="cart-container">
      <div id="cart-items"></div>
      
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; flex-wrap: wrap; gap: 15px;">
        <h2 id="total-price" style="font-family: Outfit; font-weight: 800; color: var(--primary);">Total: Rs. 0</h2>
        <div style="display: flex; gap: 10px;">
          <button onclick="clearCart()" class="btn btn-danger">Clear Cart</button>
          <a class="btn" href="checkout.php">Proceed to Checkout</a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    function getCart() {
      return JSON.parse(localStorage.getItem("cart")) || [];
    }

    function saveCart(cart) {
      localStorage.setItem("cart", JSON.stringify(cart));
    }

    function renderCart() {
      const cartItemsDiv = document.getElementById("cart-items");
      const totalPriceHeading = document.getElementById("total-price");

      const cart = getCart();
      cartItemsDiv.innerHTML = "";

      let total = 0;

      if (cart.length === 0) {
        cartItemsDiv.innerHTML = "<div style='text-align:center; padding: 40px 10px; color: var(--text-muted); font-size:1.15rem;'>Your cart is empty. Please visit the menu to add delicious items!</div>";
        totalPriceHeading.innerText = "Total: Rs. 0";
        return;
      }

      cart.forEach((item, index) => {
        const lineTotal = item.price * item.qty;
        total += lineTotal;

        const row = document.createElement("div");
        row.className = "cart-row";

        row.innerHTML = `
          <div>
            <h3 style="font-size: 1.15rem; margin-bottom: 2px;">${item.name}</h3>
            <span style="color: var(--text-muted); font-size: 0.9rem;">Rs. ${item.price} each</span>
          </div>
          <div style="display: flex; align-items: center; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
              <span style="font-weight: 700; font-size: 1.1rem; min-width: 20px; text-align: center;">${item.qty}</span>
              <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
            </div>
            <div style="font-weight: 800; font-size: 1.15rem; color: var(--text-main); min-width: 90px; text-align: right;">
              Rs. ${lineTotal}
            </div>
            <button class="btn btn-danger" style="padding: 6px 10px; font-size: 0.85rem;" onclick="removeItem(${index})">Remove</button>
          </div>
        `;

        cartItemsDiv.appendChild(row);
      });

      totalPriceHeading.innerText = "Total: Rs. " + total;
    }

    function changeQty(index, delta) {
      const cart = getCart();
      cart[index].qty += delta;

      if (cart[index].qty <= 0) {
        cart.splice(index, 1);
      }
      saveCart(cart);
      renderCart();
    }

    function removeItem(index) {
      const cart = getCart();
      cart.splice(index, 1);
      saveCart(cart);
      renderCart();
    }

    function clearCart() {
      localStorage.removeItem("cart");
      renderCart();
    }

    renderCart();
  </script>
</body>
</html>
