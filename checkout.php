<?php
include_once(__DIR__ . "/php/auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Campus Canteen</title>
    <link rel="manifest" href="manifest.json">
    <!-- FontAwesome for professional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
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
        .checkout-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
            margin-top: 20px;
        }
        .checkout-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-md);
        }
        .summary-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-md);
            height: fit-content;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        @media (max-width: 768px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }
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
                <a href="checkout.php" class="active"><i class="fa-solid fa-credit-card"></i> Checkout</a> |
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

        <h1 class="page-title">Place Your Order</h1>

        <div class="checkout-layout">
            <div class="checkout-box">
                <h2 style="margin-bottom: 20px; font-family: Outfit;">Pickup Details</h2>
                <form action="php/place_order.php" method="POST" onsubmit="return prepareOrderData();">
                    <div style="margin-bottom: 20px;">
                        <label>Your Full Name:</label>
                        <input type="text" id="customer_name" name="customer" required placeholder="Enter your name (e.g. Ali Ahmed)">
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label>Select Pickup Time:</label>
                        <input type="time" name="pickup_time" required>
                        <span style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-top: 5px;">Set the time you will arrive at the canteen to pick up your food.</span>
                    </div>

                    <!-- hidden fields -->
                    <input type="hidden" name="cart_json" id="cart_json">
                    <input type="hidden" name="total" id="total">

                    <button type="submit" class="btn" style="width: 100%; padding: 14px; font-size: 1.1rem; box-shadow: 0 4px 14px rgba(242, 100, 25, 0.4);">Confirm & Place Pre-Order</button>
                </form>
            </div>

            <div class="summary-box">
                <h2 style="margin-bottom: 20px; font-family: Outfit; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">Order Summary</h2>
                <div id="checkout-summary-items"></div>
                <div style="border-top: 2px solid var(--border-color); margin-top: 20px; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-family: Outfit; font-weight: 700;">Grand Total:</h3>
                    <h2 id="checkout-grand-total" style="font-family: Outfit; color: var(--primary); font-weight: 800;">Rs. 0</h2>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const savedName = localStorage.getItem("canteen_customer_name");
            if (savedName) {
                document.getElementById("customer_name").value = savedName;
            }

            renderSummary();
        });

        function getCart() {
            return JSON.parse(localStorage.getItem("cart")) || [];
        }

        function renderSummary() {
            const container = document.getElementById("checkout-summary-items");
            const grandTotalHeading = document.getElementById("checkout-grand-total");
            const cart = getCart();

            container.innerHTML = "";
            let total = 0;

            if (cart.length === 0) {
                container.innerHTML = "<p style='color: var(--text-muted);'>No items in cart.</p>";
                return;
            }

            cart.forEach(item => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;

                const div = document.createElement("div");
                div.className = "summary-item";
                div.innerHTML = `
                    <span><strong>${item.qty}x</strong> ${item.name}</span>
                    <span style="font-weight: 600;">Rs. ${lineTotal}</span>
                `;
                container.appendChild(div);
            });

            grandTotalHeading.innerText = "Rs. " + total;
        }

        function prepareOrderData() {
            const cart = getCart();

            if (cart.length === 0) {
                alert("Your cart is empty! Please add some delicious food items first.");
                return false;
            }

            let total = 0;
            cart.forEach(i => total += Number(i.price) * Number(i.qty));

            document.getElementById("cart_json").value = JSON.stringify(cart);
            document.getElementById("total").value = total;

            const name = document.getElementById("customer_name").value;
            localStorage.setItem("canteen_customer_name", name);

            return true;
        }
    </script>
</body>
</html>