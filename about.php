<?php
include_once(__DIR__ . "/php/auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Campus Canteen</title>
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
        .about-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-md);
            margin-top: 20px;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin: 25px 0;
        }
        .about-card {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
        }
        @media (max-width: 600px) {
            .about-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <!-- Customer Navbar -->
        <div class="navbar">
            <div class="nav-left">
                <a href="about.php" class="active" style="font-weight: bold; color: var(--primary); border-right: 2px solid var(--border-color); padding-right: 15px; margin-right: 10px;"><i class="fa-solid fa-circle-info"></i> About</a>
                <a href="menu.php"><i class="fa-solid fa-utensils"></i> Menu</a> |
                <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a> |
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

        <h1 class="page-title">Project Overview</h1>

        <div class="about-box">
            <h2 style="font-family: Outfit; color: var(--text-main); margin-bottom: 15px;">Smart Pre-Ordering System</h2>
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.7; margin-bottom: 20px;">
                The <strong>Campus Canteen PreOrder System</strong> is a professional web solution engineered to eliminate long queues and wait times during busy campus hours. By allowing students and faculty to browse the digital menu, add items to their cart, and specify a custom pickup time, we create a smooth and seamless dining experience.
            </p>

            <div class="about-grid">
                <div class="about-card">
                    <h3 style="color: var(--primary); font-family: Outfit; margin-bottom: 10px; display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-users"></i> Customer Convenience</h3>
                    <ul style="padding-left: 20px; color: var(--text-main); display:flex; flex-direction:column; gap:6px; font-size:0.95rem;">
                        <li>Zero-login checkout process</li>
                        <li>Live step-by-step order tracking</li>
                        <li>Automatic device order history</li>
                        <li>One-click quick re-ordering</li>
                    </ul>
                </div>
                <div class="about-card">
                    <h3 style="color: var(--secondary); font-family: Outfit; margin-bottom: 10px; display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-chart-line"></i> Admin & Analytics</h3>
                    <ul style="padding-left: 20px; color: var(--text-main); display:flex; flex-direction:column; gap:6px; font-size:0.95rem;">
                        <li>Securely protected admin panel</li>
                        <li>Interactive incoming orders list</li>
                        <li>Product inventory & image uploads</li>
                        <li>Python-powered combo mining</li>
                    </ul>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, var(--primary) 0%, #dd4f08 100%); color: #fff; padding: 25px; border-radius: var(--radius-md); text-align: center; margin-top: 30px;">
                <h3 style="color: #fff; font-family: Outfit; margin-bottom: 8px;">Developed by Ehsaan Ul Haq Tawakly</h3>
                <p style="opacity: 0.9; margin-bottom: 15px;">Dedicated to engineering intuitive user-centric web applications.</p>
                <a href="menu.php" class="btn" style="background:#fff; color:var(--primary); box-shadow:none; padding:10px 24px; text-decoration:none;">Browse Food Menu</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>