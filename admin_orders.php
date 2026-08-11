<?php
include "php/connect.php";
include "php/auth.php";

require_admin();

$filter = $_GET['filter'] ?? 'All';

$query = "
    SELECT o.*, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.product) SEPARATOR ', ') as items_list
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
";

if ($filter !== 'All') {
    $safe_filter = mysqli_real_escape_string($conn, $filter);
    $query .= " WHERE o.status = '$safe_filter'";
}

$query .= " GROUP BY o.order_id ORDER BY o.order_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Campus Canteen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filter-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            background: #fff;
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: var(--radius-sm);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Admin navbar -->
        <div class="navbar" style="border-left: 5px solid var(--primary);">
            <div class="nav-left">
                <a href="admin_dashboard.php">📊 Dashboard</a>
                <a href="admin_orders.php" class="active">📋 Manage Orders</a>
                <a href="admin_products.php">🍔 Canteen Menu Items</a>
                <a href="analysis.php">📈 Sales Report</a>
                <a href="admin_register.php">🕒 Daily Shift / Register</a>
                <a href="menu.php" target="_blank">👁️ Customer View</a>
            </div>
            <div>
                <a href="logout.php" class="btn btn-danger" style="padding: 8px 15px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>

        <h1 class="page-title">Manage Pre-Orders</h1>

        <div style="background: var(--card-bg); padding: 25px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); margin-top: 20px;">
            <!-- Filter buttons -->
            <div class="filter-buttons">
                <?php
                $filters = ['All', 'Pending', 'Preparing', 'Ready'];
                foreach ($filters as $f) {
                    $activeClass = ($filter === $f) ? 'active' : '';
                    echo "<a class='filter-btn {$activeClass}' href='admin_orders.php?filter=" . urlencode($f) . "'>{$f}</a>";
                }
                ?>
            </div>

            <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                <table border="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Pickup Time</th>
                            <th>Items Ordered</th>
                            <th>Total Bill</th>
                            <th>Status</th>
                            <th>Change Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { 
                            $status = $row['status'];
                            $badgeClass = '';
                            if ($status === 'Pending') $badgeClass = 'badge-pending';
                            elseif ($status === 'Preparing') $badgeClass = 'badge-preparing';
                            elseif ($status === 'Ready') $badgeClass = 'badge-ready';
                            ?>
                            <tr>
                                <td><strong>#<?php echo $row['order_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                                <td><span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($row['pickup_time']); ?></span></td>
                                <td style="max-width: 320px; font-size: 0.9rem;"><?php echo htmlspecialchars($row['items_list'] ?? 'No items details'); ?></td>
                                <td>Rs. <?php echo (int)$row['total']; ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                <td>
                                    <form action="php/update_status.php" method="POST" style="display:flex; gap: 5px; align-items: center;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                        <select name="status" style="width: auto; padding: 5px; font-size: 0.85rem;">
                                            <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Preparing" <?php echo $status === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="Ready" <?php echo $status === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                        </select>
                                        <button type="submit" class="btn" style="padding: 6px 12px; font-size: 0.85rem;">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                    No orders found matching the filter "<?php echo htmlspecialchars($filter); ?>".
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>