<?php
include "php/connect.php";
include_once "php/auth.php";

require_admin();

$error = "";
$success = "";
$username = $_SESSION['username'] ?? 'Admin';

// Handle Start / End shift actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'open_shift') {
        // Check if there is already an open register
        $check_open = mysqli_query($conn, "SELECT id FROM daily_registers WHERE status = 'Open' LIMIT 1");
        if ($check_open && mysqli_num_rows($check_open) > 0) {
            $error = "A shift register is already open! Close the current shift first.";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO daily_registers (status, opened_by) VALUES ('Open', ?)");
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Business shift started successfully. Customers can place orders under this shift.";
            } else {
                $error = "Failed to start business shift register.";
            }
        }
    } elseif ($action === 'close_shift') {
        // Check active open register
        $check_open = mysqli_query($conn, "SELECT id FROM daily_registers WHERE status = 'Open' LIMIT 1");
        if ($check_open && mysqli_num_rows($check_open) > 0) {
            $reg = mysqli_fetch_assoc($check_open);
            $reg_id = (int)$reg['id'];
            
            // Set status to Closed and update closed_at timestamp
            $stmt = mysqli_prepare($conn, "UPDATE daily_registers SET status = 'Closed', closed_at = CURRENT_TIMESTAMP WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $reg_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Business shift register ID #{$reg_id} closed successfully.";
            } else {
                $error = "Failed to close business shift register.";
            }
        } else {
            $error = "No active shift is currently open.";
        }
    }
}

// 1. Fetch current open register if exists
$active_shift = null;
$active_orders_count = 0;
$active_revenue = 0;

$check_active = mysqli_query($conn, "SELECT * FROM daily_registers WHERE status = 'Open' LIMIT 1");
if ($check_active && mysqli_num_rows($check_active) > 0) {
    $active_shift = mysqli_fetch_assoc($check_active);
    $active_id = (int)$active_shift['id'];
    
    // Calculate live orders and revenue for this shift
    $stats_res = mysqli_query($conn, "SELECT COUNT(order_id) AS total_orders, COALESCE(SUM(total), 0) AS total_revenue FROM orders WHERE register_id = $active_id");
    if ($stats_res) {
        $stats = mysqli_fetch_assoc($stats_res);
        $active_orders_count = (int)$stats['total_orders'];
        $active_revenue = (int)$stats['total_revenue'];
    }
}

// 2. Fetch history of shifts
$history_query = "
    SELECT r.*, 
           COUNT(o.order_id) AS total_orders, 
           COALESCE(SUM(o.total), 0) AS total_revenue 
    FROM daily_registers r
    LEFT JOIN orders o ON o.register_id = r.id
    GROUP BY r.id
    ORDER BY r.id DESC
";
$history_res = mysqli_query($conn, $history_query);
$shift_history = [];
while ($row = $history_res ? mysqli_fetch_assoc($history_res) : null) {
    if ($row) $shift_history[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Register | Campus Canteen</title>
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .shift-status-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-dot.active {
            background: var(--accent-success);
            animation: pulse-dot 1.5s infinite;
        }
        .status-dot.inactive {
            background: #94a3b8;
        }
        @keyframes pulse-dot {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }
        .shift-metrics {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .shift-metric-item {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 15px 25px;
            border-radius: var(--radius-md);
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
                <a href="analysis.php">📈 Sales Report</a>
                <a href="admin_register.php" class="active">🕒 Daily Shift / Register</a>
                <a href="menu.php" target="_blank">👁️ Customer View</a>
            </div>
            <div>
                <a href="logout.php" class="btn btn-danger" style="padding: 8px 15px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>

        <h1 style="font-family: Outfit; font-size: 2rem; margin-bottom: 25px;">Shift Session Management</h1>

        <?php if ($error !== "") { ?>
            <div class="alert alert-danger" style="color: #d93025; background: #fde8e8; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; border: 1px solid #f8b4b4; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <?php if ($success !== "") { ?>
            <div class="alert alert-success" style="color: #137333; background: #e6f4ea; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; border: 1px solid #c2e7c9; margin-bottom: 15px;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php } ?>

        <!-- Active Shift Section -->
        <div class="shift-status-card">
            <div>
                <?php if ($active_shift) { ?>
                    <h2 style="font-family: Outfit; margin: 0 0 5px; display: flex; align-items: center;">
                        <span class="status-dot active"></span>
                        Active Business Shift #<?php echo (int)$active_shift['id']; ?>
                    </h2>
                    <p style="color: var(--text-muted); margin: 0 0 15px;">Opened At: <b><?php echo htmlspecialchars($active_shift['opened_at']); ?></b> by <b><?php echo htmlspecialchars($active_shift['opened_by']); ?></b></p>
                    
                    <div class="shift-metrics">
                        <div class="shift-metric-item">
                            <span style="font-size:0.85rem; color:var(--text-muted); font-weight:600; display:block; text-transform:uppercase;">Shift Orders</span>
                            <span style="font-family: Outfit; font-size:1.5rem; font-weight:800; color:var(--primary);"><?php echo $active_orders_count; ?> Placed</span>
                        </div>
                        <div class="shift-metric-item">
                            <span style="font-size:0.85rem; color:var(--text-muted); font-weight:600; display:block; text-transform:uppercase;">Shift Revenue</span>
                            <span style="font-family: Outfit; font-size:1.5rem; font-weight:800; color:var(--accent-success);">Rs. <?php echo number_format($active_revenue); ?></span>
                        </div>
                    </div>
                <?php } else { ?>
                    <h2 style="font-family: Outfit; margin: 0 0 5px; display: flex; align-items: center; color: var(--text-muted);">
                        <span class="status-dot inactive"></span>
                        Canteen Register Closed
                    </h2>
                    <p style="color: var(--text-muted); margin: 0;">Open a new register shift to start recording orders and tracking revenue for the day.</p>
                <?php } ?>
            </div>

            <div>
                <?php if ($active_shift) { ?>
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to CLOSE this business day shift? Initial shift details and sales totals will be logged.');">
                        <input type="hidden" name="action" value="close_shift">
                        <button type="submit" class="btn btn-danger" style="padding: 15px 30px; font-weight: 700; box-shadow: 0 4px 15px rgba(229, 62, 62, 0.25);">End Shift / Close Canteen</button>
                    </form>
                <?php } else { ?>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="open_shift">
                        <button type="submit" class="btn" style="padding: 15px 30px; font-weight: 700; background: var(--primary); box-shadow: 0 4px 15px rgba(242, 100, 25, 0.25);">Start Business Day / Open Shift</button>
                    </form>
                <?php } ?>
            </div>
        </div>

        <!-- Shift Log History -->
        <h2 style="font-family: Outfit; margin-bottom: 15px;">Business Day Shift Logs</h2>
        <div class="history-box" style="margin-top: 0; padding: 20px;">
            <table border="0">
                <thead>
                    <tr>
                        <th>Shift ID</th>
                        <th>Opened At</th>
                        <th>Closed At</th>
                        <th>Shift Duration</th>
                        <th>Total Orders</th>
                        <th>Total Revenue</th>
                        <th>Opened By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($shift_history) > 0) {
                        foreach ($shift_history as $s) {
                            $opened = strtotime($s['opened_at']);
                            $closed = $s['closed_at'] ? strtotime($s['closed_at']) : time();
                            $diff = $closed - $opened;
                            
                            $hours = floor($diff / 3600);
                            $mins = floor(($diff % 3600) / 60);
                            
                            $duration = "";
                            if ($hours > 0) $duration .= "{$hours}h ";
                            $duration .= "{$mins}m";
                            if (!$s['closed_at']) $duration = "<i>Active</i>";
                            
                            $badgeClass = $s['status'] === 'Open' ? 'badge-ready' : 'badge-pending';
                            ?>
                            <tr>
                                <td><strong>#<?php echo (int)$s['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($s['opened_at']); ?></td>
                                <td><?php echo $s['closed_at'] ? htmlspecialchars($s['closed_at']) : '<span style="color:var(--primary); font-weight:600;">Shift Active</span>'; ?></td>
                                <td><?php echo $duration; ?></td>
                                <td><strong><?php echo (int)$s['total_orders']; ?></strong></td>
                                <td><span style="font-weight: 700; color: var(--accent-success);">Rs. <?php echo number_format($s['total_revenue']); ?></span></td>
                                <td><?php echo htmlspecialchars($s['opened_by']); ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($s['status']); ?></span></td>
                            </tr>
                            <?php
                        }
                    } else { ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 25px;">No shifts logged in history yet.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
