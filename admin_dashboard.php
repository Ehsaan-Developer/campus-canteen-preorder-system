<?php
include "php/connect.php";
include "php/auth.php";

require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Campus Canteen</title>
    <!-- FontAwesome for professional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .fade-in-row {
            animation: fadeInRow 0.5s ease-out;
        }
        @keyframes fadeInRow {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .live-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--accent-success);
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse-live 1.5s infinite;
        }
        @keyframes pulse-live {
            0% { transform: scale(0.9); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Admin navbar with FontAwesome icons -->
        <div class="navbar" style="border-left: 5px solid var(--primary);">
            <div class="nav-left">
                <a href="admin_dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <a href="admin_orders.php"><i class="fa-solid fa-list-check"></i> Manage Orders</a>
                <a href="admin_products.php"><i class="fa-solid fa-utensils"></i> Canteen Menu Items</a>
                <a href="analysis.php"><i class="fa-solid fa-chart-simple"></i> Sales Report</a>
                <a href="admin_register.php"><i class="fa-solid fa-clock-rotate-left"></i> Shift Register</a>
                <a href="menu.php" target="_blank"><i class="fa-solid fa-eye"></i> Customer View</a>
            </div>
            <div>
                <span style="font-weight: 600; margin-right: 15px; color: var(--text-muted);"><i class="fa-solid fa-user-tie"></i> Hello, Admin</span>
                <a href="logout.php" class="btn btn-danger" style="padding: 8px 15px; font-size: 0.9rem;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <div class="dashboard-header">
            <h1 style="font-family: Outfit; font-size: 2.2rem; font-weight: 800; color: #1e293b;"><i class="fa-solid fa-gauge-high"></i> Control Dashboard</h1>
            <div class="admin-nav">
                <a href="admin_add_product.php" class="btn"><i class="fa-solid fa-plus"></i> Add Food Item</a>
                <a href="analysis.php" class="btn btn-secondary"><i class="fa-solid fa-chart-pie"></i> Sales Report Analytics</a>
            </div>
        </div>

        <!-- Metrics cards grid (updates live via JS) -->
        <div class="stats-grid">
            <div class="stats-card" style="border-top: 4px solid var(--primary);">
                <div class="stats-title">Total Revenue</div>
                <div class="stats-value" id="lbl-revenue">Rs. 0</div>
            </div>
            <div class="stats-card" style="border-top: 4px solid var(--secondary);">
                <div class="stats-title">Active Pre-Orders</div>
                <div class="stats-value" id="lbl-active">0</div>
            </div>
            <div class="stats-card" style="border-top: 4px solid var(--accent-success);">
                <div class="stats-title">Ready for Collection</div>
                <div class="stats-value" id="lbl-ready">0</div>
            </div>
            <div class="stats-card" style="border-top: 4px solid var(--text-muted);">
                <div class="stats-title">Total Menu Items</div>
                <div class="stats-value" id="lbl-products">0</div>
            </div>
        </div>

        <!-- Active incoming orders table container (updates live via JS) -->
        <div class="admin-layout">
            <div style="background: var(--card-bg); padding: 25px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
                <h2 style="font-family: Outfit; margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fa-solid fa-bell"></i> Incoming Live Orders (Pending/Preparing)</span>
                    <span style="font-size:0.9rem; color:var(--text-muted); font-weight:600; display:flex; align-items:center;">
                        <span class="live-dot"></span> Auto-Updating Live...
                    </span>
                </h2>

                <div id="orders-table-container">
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 10px; display:block;"></i>
                        Loading orders...
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <script>
        function loadDashboardData() {
            fetch('php/get_dashboard_data.php')
                .then(res => res.json())
                .then(data => {
                    // 1. Update Metrics
                    document.getElementById('lbl-revenue').innerText = 'Rs. ' + Number(data.metrics.total_revenue).toLocaleString();
                    document.getElementById('lbl-active').innerText = data.metrics.active_orders;
                    document.getElementById('lbl-ready').innerText = data.metrics.ready_orders;
                    document.getElementById('lbl-products').innerText = data.metrics.total_products;

                    // 2. Update Table
                    const tableContainer = document.getElementById('orders-table-container');
                    const orders = data.orders;

                    if (orders.length === 0) {
                        tableContainer.innerHTML = `
                            <div style="text-align: center; padding: 45px 20px; color: var(--text-muted); font-size: 1.1rem; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                                <i class="fa-solid fa-circle-check" style="font-size: 2.8rem; color: var(--accent-success); margin-bottom: 12px; display: block;"></i>
                                All orders have been completed! No pending pre-orders.
                            </div>
                        `;
                        return;
                    }

                    let html = `
                        <table border="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Pickup Time</th>
                                    <th>Items Ordered</th>
                                    <th>Total Bill</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    orders.forEach(o => {
                        const badgeClass = o.status === 'Pending' ? 'badge-pending' : 'badge-preparing';
                        const actionBtn = o.status === 'Pending' 
                            ? `<button class="btn btn-secondary" onclick="updateStatus(${o.order_id}, 'Preparing')" style="padding: 6px 12px; font-size: 0.85rem;"><i class="fa-solid fa-fire-burner"></i> Prepare</button>`
                            : `<button class="btn" onclick="updateStatus(${o.order_id}, 'Ready')" style="padding: 6px 12px; font-size: 0.85rem; background: var(--accent-success);"><i class="fa-solid fa-circle-check"></i> Ready</button>`;

                        html += `
                            <tr class="fade-in-row">
                                <td><strong>#${o.order_id}</strong></td>
                                <td>${escapeHtml(o.customer)}</td>
                                <td><span style="color: var(--primary); font-weight: 600;"><i class="fa-regular fa-clock"></i> ${escapeHtml(o.pickup_time)}</span></td>
                                <td style="max-width: 300px; font-size: 0.9rem;">${escapeHtml(o.items_list)}</td>
                                <td>Rs. ${o.total.toLocaleString()}</td>
                                <td><span class="badge ${badgeClass}">${o.status}</span></td>
                                <td>
                                    <div style="display:inline-block;">
                                        ${actionBtn}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;
                    tableContainer.innerHTML = html;
                })
                .catch(err => console.error("Error loading dashboard data:", err));
        }

        function updateStatus(orderId, status) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', status);
            formData.append('ajax', '1');

            fetch('php/update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    loadDashboardData();
                }
            })
            .catch(err => console.error("Error updating order status:", err));
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

        // Initial Load and poll interval
        loadDashboardData();
        setInterval(loadDashboardData, 5000);
    </script>
</body>
</html>
