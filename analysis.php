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
    <title>Sales Analytics | Campus Canteen</title>
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-layout {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 25px;
            margin-top: 25px;
        }
        .analytics-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
        }
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
        }
        @media (max-width: 900px) {
            .analytics-layout {
                grid-template-columns: 1fr;
            }
        }
        
        /* Flash animation for live updates */
        .fade-updated {
            animation: flashHighlight 1.5s ease-out;
        }
        @keyframes flashHighlight {
            0% { background-color: rgba(242, 100, 25, 0.15); }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Admin navbar -->
        <div class="navbar" style="border-left: 5px solid var(--primary);">
            <div class="nav-left">
                <a href="admin_dashboard.php">📊 Dashboard</a>
                <a href="admin_orders.php">📋 Manage Orders</a>
                <a href="admin_products.php">🍔 Canteen Menu Items</a>
                <a href="analysis.php" class="active">📈 Sales Report</a>
                <a href="admin_register.php">🕒 Daily Shift / Register</a>
                <a href="menu.php" target="_blank">👁️ Customer View</a>
            </div>
            <div>
                <a href="logout.php" class="btn btn-danger" style="padding: 8px 15px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom: 25px;">
            <h1 style="font-family: Outfit; font-size: 2rem; margin: 0;">Live Sales Intelligence</h1>
            <div style="display:flex; align-items:center; gap:8px;">
                <span id="sync-status" style="font-size:0.9rem; color:var(--text-muted); font-weight:600; display:flex; align-items:center; gap:6px;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--accent-success); animation: pulse 1.5s infinite;"></span>
                    Live Auto-Updating...
                </span>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="stats-grid">
            <div class="stats-card" id="card-orders" style="border-top: 4px solid var(--primary); transition: background-color 0.5s;">
                <div class="stats-title">Analyzed Orders</div>
                <div class="stats-value" id="lbl-total-orders">0</div>
            </div>
            <div class="stats-card" id="card-revenue" style="border-top: 4px solid var(--accent-success); transition: background-color 0.5s;">
                <div class="stats-title">Analyzed Revenue</div>
                <div class="stats-value" id="lbl-total-revenue">Rs. 0</div>
            </div>
        </div>

        <div class="analytics-layout">
            <!-- Top Products Chart -->
            <div class="analytics-box">
                <h2 style="font-family: Outfit; margin-bottom: 20px; color: var(--text-main); border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">🔥 Product Sales Volume</h2>
                <div class="chart-container">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            <!-- Combo Recommendations -->
            <div class="analytics-box">
                <h2 style="font-family: Outfit; margin-bottom: 20px; color: var(--text-main); border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">💡 Smart Combo Recommendations</h2>
                <div style="flex-grow: 1; display:flex; flex-direction:column; justify-content:center;">
                    <ul id="lbl-top-combos" style="padding-left: 20px; display:flex; flex-direction:column; gap:12px; margin: 10px 0;">
                        <!-- Dynamically filled -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <script>
        let chartInstance = null;
        let previousData = {
            total_orders: -1,
            total_revenue: -1,
            top_items_hash: '',
            top_combos_hash: ''
        };

        // Number animation function
        function animateValue(obj, start, end, duration, prefix = "") {
            if (start === end) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = prefix + Math.floor(progress * (end - start) + start).toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function getJSONHash(obj) {
            return JSON.stringify(obj);
        }

        function loadAnalyticsData() {
            fetch('php/get_analytics.php')
                .then(res => res.json())
                .then(data => {
                    const lblOrders = document.getElementById("lbl-total-orders");
                    const lblRev = document.getElementById("lbl-total-revenue");
                    const cardOrders = document.getElementById("card-orders");
                    const cardRev = document.getElementById("card-revenue");

                    const orders = Number(data.total_orders || 0);
                    const revenue = Number(data.total_revenue || 0);

                    // Update metrics
                    if (orders !== previousData.total_orders) {
                        animateValue(lblOrders, previousData.total_orders === -1 ? 0 : previousData.total_orders, orders, 800);
                        if (previousData.total_orders !== -1) {
                            cardOrders.classList.add("fade-updated");
                            setTimeout(() => cardOrders.classList.remove("fade-updated"), 1500);
                        }
                        previousData.total_orders = orders;
                    }

                    if (revenue !== previousData.total_revenue) {
                        animateValue(lblRev, previousData.total_revenue === -1 ? 0 : previousData.total_revenue, revenue, 800, "Rs. ");
                        if (previousData.total_revenue !== -1) {
                            cardRev.classList.add("fade-updated");
                            setTimeout(() => cardRev.classList.remove("fade-updated"), 1500);
                        }
                        previousData.total_revenue = revenue;
                    }

                    // Update Chart
                    const topItems = data.top_items || [];
                    const itemsHash = getJSONHash(topItems);
                    
                    if (itemsHash !== previousData.top_items_hash) {
                        const labels = topItems.map(i => i.product);
                        const values = topItems.map(i => i.qty);

                        updateChart(labels, values);
                        previousData.top_items_hash = itemsHash;
                    }

                    // Update Combos
                    const topCombos = data.top_combos || [];
                    const combosHash = getJSONHash(topCombos);

                    if (combosHash !== previousData.top_combos_hash) {
                        const list = document.getElementById("lbl-top-combos");
                        list.innerHTML = "";
                        
                        if (topCombos.length > 0) {
                            topCombos.forEach(c => {
                                const li = document.createElement("li");
                                li.style.fontSize = "1.05rem";
                                li.innerHTML = `
                                    <strong>${escapeHtml(c.item1)}</strong> + 
                                    <strong>${escapeHtml(c.item2)}</strong>
                                    <span style="color: var(--primary); font-weight:600; font-size: 0.9rem;"> (Pairings: ${c.count} times)</span>
                                `;
                                list.appendChild(li);
                            });
                        } else {
                            list.innerHTML = "<li style='color:var(--text-muted); list-style:none; text-align:center;'>Not enough combinations yet. Place more multi-item orders.</li>";
                        }
                        previousData.top_combos_hash = combosHash;
                    }
                })
                .catch(err => {
                    console.error("Error loading sales analytics:", err);
                });
        }

        function updateChart(labels, data) {
            const ctx = document.getElementById('topProductsChart').getContext('2d');
            
            if (chartInstance) {
                chartInstance.data.labels = labels;
                chartInstance.data.datasets[0].data = data;
                chartInstance.update();
            } else {
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Quantity Sold',
                            data: data,
                            backgroundColor: 'rgba(242, 100, 25, 0.75)',
                            borderColor: 'rgb(242, 100, 25)',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { precision: 0 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        const style = document.createElement('style');
        style.type = 'text/css';
        style.innerHTML = '@keyframes pulse { 0% { transform: scale(0.95); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(0.95); opacity: 0.5; } }';
        document.getElementsByTagName('head')[0].appendChild(style);

        // Initial Load
        loadAnalyticsData();
        
        // Auto update every 8 seconds
        setInterval(loadAnalyticsData, 8000);
    </script>
</body>
</html>