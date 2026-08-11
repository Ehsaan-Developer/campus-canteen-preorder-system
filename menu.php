<?php
include(__DIR__ . "/php/connect.php");
include_once(__DIR__ . "/php/auth.php");

// Category filter
$category = $_GET['category'] ?? 'All';
$search = trim($_GET['search'] ?? '');

$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if ($category !== 'All') {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Canteen Menu</title>
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
        .filter-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.7);
            padding: 15px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }
        .categories {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            background: #fff;
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .category-btn.active, .category-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .search-bar {
            display: flex;
            gap: 10px;
            max-width: 350px;
            width: 100%;
        }
        .search-input {
            flex-grow: 1;
        }

        /* --- Cart Drawer --- */
        #floating-cart-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(242, 100, 25, 0.35);
            cursor: pointer;
            font-weight: 700;
            z-index: 100;
            transition: transform 0.2s, background 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            border: none;
        }
        #floating-cart-btn:hover {
            transform: translateY(-3px);
            background: var(--primary-hover);
        }

        #cart-drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 380px;
            max-width: 100%;
            height: 100vh;
            background: #fff;
            box-shadow: -10px 0 40px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--border-color);
        }
        #cart-drawer.open {
            right: 0;
        }

        #cart-drawer-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 999;
            display: none;
        }
        #cart-drawer-backdrop.open {
            display: block;
        }

        .drawer-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        .close-drawer {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: var(--text-muted);
            line-height: 1;
        }
        #drawer-items-list {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .drawer-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: #f8fafc;
        }
        .drawer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        .drawer-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .qty-btn {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: #fff;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        
        @keyframes buttonPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); background-color: var(--accent-success); box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4); }
            100% { transform: scale(1); }
        }
        #floating-cart-btn.pulse {
            animation: buttonPulse 0.4s ease;
        }
    </style>
</head>
<body>


    <div class="container">
        <!-- Customer Navbar -->
        <div class="navbar" style="flex-wrap: wrap;">
            <div class="nav-left">
                <a href="about.php" style="font-weight: bold; color: var(--primary); border-right: 2px solid var(--border-color); padding-right: 15px; margin-right: 10px;"><i class="fa-solid fa-circle-info"></i> About</a>
                <a href="menu.php" class="active"><i class="fa-solid fa-utensils"></i> Menu</a> |
                <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a> |
                <a href="checkout.php"><i class="fa-solid fa-credit-card"></i> Checkout</a> |
                <a href="track_order.php"><i class="fa-solid fa-truck-ramp-box"></i> Track Order</a> |
                <a href="order_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a> |
                <a href="login.php" style="font-size: 0.95rem; font-weight: 500; color: var(--text-main);"><i class="fa-solid fa-user-shield"></i> Admin Portal</a>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: nowrap; flex-shrink: 0;">
                <span style="font-size: 0.85rem; color: var(--accent-success); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.08); padding: 5px 12px; border-radius: 50px; white-space: nowrap;">
                    <span class="live-dot"></span>
                    Live Connected
                </span>
                <a class="cart-btn" href="javascript:void(0)" onclick="toggleCartDrawer(true)" style="white-space: nowrap;"><i class="fa-solid fa-basket-shopping"></i> Basket <span id="cart-count" style="background: #fff; color: var(--primary); padding: 2px 7px; border-radius: 50%; font-size: 0.8rem; margin-left: 5px; font-weight: bold;">0</span></a>
            </div>
        </div>

        <h1 class="page-title">Digital Campus Menu</h1>
        <p style="text-align: center; color: var(--text-muted); margin-bottom: 30px; font-size: 1.1rem;">Pre-order your favorite meal and pick it up on time!</p>

        <!-- Search and Filter Panel -->
        <div class="filter-section">
            <div class="categories">
                <?php
                $cats = ['All', 'Fast Food', 'Meals', 'Beverages', 'Snacks'];
                foreach ($cats as $c) {
                    $activeClass = ($category === $c) ? 'active' : '';
                    $searchParam = ($search !== '') ? '&search=' . urlencode($search) : '';
                    echo "<a class='category-btn {$activeClass}' href='menu.php?category=" . urlencode($c) . $searchParam . "'>{$c}</a>";
                }
                ?>
            </div>
            <form class="search-bar" method="GET" action="menu.php">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <input class="search-input" type="text" name="search" placeholder="Search dish..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = $row['name'] ?? '';
                    $price = (int)($row['price'] ?? 0);
                    $img = $row['image'] ?? '';
                    $cat = $row['category'] ?? 'Fast Food';
                    $safeName = addslashes($name);

                    echo "<div class='menu-card'>";
                    if ($img !== '' && file_exists(__DIR__ . '/' . $img)) {
                        echo "<img class='menu-card-image' src='" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($name) . "'>";
                    } else {
                        echo "<div class='menu-card-image' style='display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ffe5d9 0%, #fff0eb 100%); color: var(--primary); font-size: 3rem; font-weight: bold; font-family: Outfit;'>🍔</div>";
                    }
                    echo "<span class='badge' style='background: rgba(242, 100, 25, 0.08); color: var(--primary); font-size: 0.75rem; width: fit-content; margin-bottom: 8px; border-radius: 4px; padding: 3px 8px; font-weight: 600; text-transform: uppercase;'>" . htmlspecialchars($cat) . "</span>";
                    echo "<h3 style='margin: 0 0 4px;'>" . htmlspecialchars($name) . "</h3>";
                    echo "<div class='price'>Rs. " . $price . "</div>";
                    echo "<button class='btn' onclick=\"addToCart('{$safeName}', {$price})\">Add to Cart</button>";
                    echo "</div>";
                }
            } else {
                echo "<div style='grid-column: 1/-1; text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid var(--border-color); color: var(--text-muted); font-size: 1.1rem;'>No items found in this category.</div>";
            }
            ?>
        </div>
    </div>

    <!-- --- Floating Cart Button & Drawer Elements --- -->
    <button id="floating-cart-btn" onclick="toggleCartDrawer(true)">
        🛒 Cart (<span id="floating-cart-count">0</span>)
    </button>

    <div id="cart-drawer-backdrop" onclick="toggleCartDrawer(false)"></div>

    <div id="cart-drawer">
        <div class="drawer-header">
            <h2 style="font-family: Outfit; font-weight: 700; margin: 0; font-size: 1.4rem;">Your Basket</h2>
            <button class="close-drawer" onclick="toggleCartDrawer(false)">&times;</button>
        </div>
        <div id="drawer-items-list"></div>
        <!-- Recommendations Section (dynamic cross-sell) -->
        <div id="drawer-recommendations-wrapper" style="padding: 15px 20px; border-top: 1px solid var(--border-color); background: #fff;">
            <h3 style="font-family: Outfit; font-size: 0.95rem; margin: 0 0 10px 0; color: var(--text-main); display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary);"></i> Add a Treat?</h3>
            <div id="drawer-recommendations" style="display: flex; flex-direction: column; gap: 4px;">
                <!-- Filled dynamically via JS -->
            </div>
        </div>
        <div class="drawer-footer">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="font-weight: 600; color: var(--text-muted);">Basket Subtotal:</span>
                <span id="drawer-total-price" style="font-family: Outfit; font-weight: 800; color: var(--primary); font-size: 1.3rem;">Rs. 0</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="checkout.php" class="btn" style="text-align: center; text-decoration: none; padding: 12px;">Proceed to Checkout</a>
                <button onclick="clearDrawerCart()" class="btn btn-danger" style="padding: 10px;">Clear Basket</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" style="position:fixed; bottom:30px; left:30px; z-index:9999; display:flex; flex-direction:column; gap:10px;"></div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Drawer Rendering Script -->
    <script>
        function getCart() {
            return JSON.parse(localStorage.getItem("cart")) || [];
        }

        function saveCart(cart) {
            localStorage.setItem("cart", JSON.stringify(cart));
        }

        function addToCart(name, price) {
            let cart = getCart();
            const existing = cart.find(i => i.name === name);

            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ name, price: Number(price), qty: 1 });
            }

            saveCart(cart);
            renderDrawerCart();
            
            // Visual feedback: animate floating cart button
            const btn = document.getElementById("floating-cart-btn");
            if (btn) {
                btn.classList.remove("pulse");
                void btn.offsetWidth; // Trigger reflow
                btn.classList.add("pulse");
            }
            
            // Show toast notification
            showToast(`🛒 ${name} added to basket!`);
        }

        function showToast(msg) {
            const container = document.getElementById("toast-container");
            if (!container) return;
            
            const toast = document.createElement("div");
            toast.style.background = "#2c3e50";
            toast.style.color = "#fff";
            toast.style.padding = "12px 20px";
            toast.style.borderRadius = "8px";
            toast.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
            toast.style.fontSize = "0.95rem";
            toast.style.fontWeight = "600";
            toast.style.opacity = "0";
            toast.style.transition = "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)";
            toast.style.transform = "translateY(20px)";
            toast.innerText = msg;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = "1";
                toast.style.transform = "translateY(0)";
            }, 50);
            
            setTimeout(() => {
                toast.style.opacity = "0";
                toast.style.transform = "translateY(-20px)";
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 2000);
        }

        function toggleCartDrawer(open) {
            const drawer = document.getElementById("cart-drawer");
            const backdrop = document.getElementById("cart-drawer-backdrop");
            if (open) {
                drawer.classList.add("open");
                backdrop.classList.add("open");
            } else {
                drawer.classList.remove("open");
                backdrop.classList.remove("open");
            }
        }

        function renderDrawerCart() {
            const list = document.getElementById("drawer-items-list");
            const totalSpan = document.getElementById("drawer-total-price");
            const cartCountSpan = document.getElementById("cart-count");
            const floatCountSpan = document.getElementById("floating-cart-count");
            
            const cart = getCart();
            list.innerHTML = "";
            let total = 0;
            let count = 0;
            
            if (cart.length === 0) {
                list.innerHTML = "<div style='text-align:center; padding: 40px 10px; color: var(--text-muted); font-size:1.05rem;'>Your basket is empty.<br><br>Add food items from the menu to build your pre-order!</div>";
                totalSpan.innerText = "Rs. 0";
                cartCountSpan.innerText = "0";
                floatCountSpan.innerText = "0";
                loadDrawerRecommendations();
                return;
            }
            
            cart.forEach((item, index) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;
                count += item.qty;
                
                const div = document.createElement("div");
                div.className = "drawer-item";
                div.innerHTML = `
                    <div>
                        <strong style="display:block; font-size:0.95rem; color:var(--text-main);">${item.name}</strong>
                        <span style="color:var(--text-muted); font-size:0.85rem;">Rs. ${item.price} each</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button class="qty-btn" onclick="changeDrawerQty(${index}, -1)">-</button>
                        <span style="font-weight:700; font-size:0.95rem; min-width: 15px; text-align: center;">${item.qty}</span>
                        <button class="qty-btn" onclick="changeDrawerQty(${index}, 1)">+</button>
                        <span style="font-weight:800; font-size:0.95rem; color:var(--text-main); margin-left:8px; min-width:65px; text-align:right;">Rs. ${lineTotal}</span>
                    </div>
                `;
                list.appendChild(div);
            });
            
            totalSpan.innerText = "Rs. " + total;
            cartCountSpan.innerText = count;
            floatCountSpan.innerText = count;
            
            loadDrawerRecommendations();
        }

        function loadDrawerRecommendations() {
            fetch('php/get_recommendations.php')
                .then(res => res.json())
                .then(items => {
                    const container = document.getElementById('drawer-recommendations');
                    if (!container) return;
                    
                    const cart = getCart();
                    // Filter out items already in the cart
                    const filteredItems = items.filter(recItem => !cart.some(cartItem => cartItem.name === recItem.name));
                    
                    if (filteredItems.length === 0) {
                        container.innerHTML = `<div style="font-size:0.85rem; color:var(--text-muted); font-style:italic; text-align:center; padding:10px 0;">No other recommendations.</div>`;
                        return;
                    }
                    
                    let html = '';
                    filteredItems.slice(0, 2).forEach(item => {
                        const imgTag = item.image 
                            ? `<img src="${item.image}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">`
                            : `<div style="width: 40px; height: 40px; background: #fff0eb; display:flex; align-items:center; justify-content:center; border-radius: 6px; color: var(--primary); font-size:1.1rem;"><i class="fa-solid fa-bowl-food"></i></div>`;
                        
                        const safeName = item.name.replace(/'/g, "\\'");
                        
                        html += `
                            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(0,0,0,0.02); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-top:5px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    ${imgTag}
                                    <div>
                                        <strong style="font-size: 0.85rem; display: block; line-height: 1.2;">${escapeHtml(item.name)}</strong>
                                        <span style="font-size: 0.85rem; color: var(--primary); font-weight:700;">Rs. ${item.price}</span>
                                    </div>
                                </div>
                                <button class="btn btn-secondary" onclick="addToCart('${safeName}', ${item.price})" style="padding: 5px 10px; font-size: 0.75rem; border-radius: 4px; box-shadow:none;"><i class="fa-solid fa-plus"></i> Add</button>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                })
                .catch(err => console.error("Error loading recommendations:", err));
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function changeDrawerQty(index, delta) {
            let cart = getCart();
            cart[index].qty += delta;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
            renderDrawerCart();
        }

        function clearDrawerCart() {
            localStorage.removeItem("cart");
            renderDrawerCart();
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderDrawerCart();
        });
    </script>
</body>
</html>