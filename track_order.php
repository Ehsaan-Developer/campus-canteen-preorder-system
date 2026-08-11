<?php
include "php/connect.php";
include_once "php/auth.php";

$order = null;
$error = "";
$order_id = 0;

if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];

    $res = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $order = mysqli_fetch_assoc($res);
    } else {
        $error = "Order ID #{$order_id} not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Order | Campus Canteen</title>
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
    .track-box {
      max-width: 600px;
      margin: 30px auto;
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow-md);
    }
    .status-alert {
      padding: 15px;
      border-radius: var(--radius-sm);
      margin: 20px 0;
      text-align: center;
      font-weight: 600;
      font-size: 1.05rem;
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
        <a href="track_order.php" class="active"><i class="fa-solid fa-truck-ramp-box"></i> Track Order</a> |
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

    <h1 class="page-title">Track Your Canteen Order</h1>

    <div class="track-box">
      <form method="GET" action="" style="display: flex; gap: 10px; margin-bottom: 20px; align-items: flex-end;">
        <div style="flex-grow: 1;">
          <label style="font-weight: 600;">Enter Order ID:</label>
          <input type="number" name="order_id" value="<?php echo $order_id > 0 ? $order_id : ''; ?>" required placeholder="e.g. 23">
        </div>
        <button type="submit" class="btn" style="padding: 12px 24px;">Track</button>
      </form>

      <?php if ($error != "") { ?>
        <div class="alert alert-danger" style="color: var(--accent-danger); background: #fef2f2; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #fecaca;">
          <?php echo $error; ?>
        </div>
      <?php } ?>

      <?php if ($order) { 
        $status = $order['status'];
        ?>
        <div style="border-top: 1px solid var(--border-color); margin-top: 35px; padding-top: 25px;" id="order-details-container">
          <h2 style="font-family: Outfit; margin-bottom: 20px;">Order Details (#<?php echo $order['order_id']; ?>)</h2>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8fafc; padding: 15px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
            <p><strong>Customer:</strong> <span id="lbl-customer"><?php echo htmlspecialchars($order['customer']); ?></span></p>
            <p><strong>Pickup Time:</strong> <span id="lbl-pickup"><?php echo htmlspecialchars($order['pickup_time']); ?></span></p>
            <p><strong>Total Bill:</strong> <span id="lbl-total">Rs. <?php echo (int)$order['total']; ?></span></p>
            <p><strong>Status:</strong> <span id="lbl-status" style="font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($status); ?></span></p>
          </div>

          <!-- Step Progress Tracker -->
          <div class="tracker-container">
            <h3 style="text-align: center; font-family: Outfit; margin-bottom: 5px;">Preparation Progress</h3>
            
            <div class="steps-bar">
              <div class="step-progress-line" id="progress-line" style="width: 0%;"></div>
              
              <div class="step-node" id="step-pending">
                1
                <div class="step-label">Pending</div>
              </div>
              
              <div class="step-node" id="step-preparing">
                2
                <div class="step-label">Preparing</div>
              </div>
              
              <div class="step-node" id="step-ready">
                3
                <div class="step-label">Ready</div>
              </div>
            </div>
          </div>

          <!-- Celebratory Alert -->
          <div id="ready-celebration" class="status-alert" style="display: none; background-color: #d1fae5; border: 1px solid #a7f3d0; color: #065f46;">
            🎉 <strong>Your food is ready!</strong> Please proceed to the canteen counter to pick it up. Enjoy your meal!
          </div>

          <div id="preparing-alert" class="status-alert" style="display: none; background-color: #dbeafe; border: 1px solid #bfdbfe; color: #1e40af;">
            👨‍🍳 <strong>Canteen staff is preparing your food.</strong> Almost there!
          </div>

          <div id="pending-alert" class="status-alert" style="display: none; background-color: #fef3c7; border: 1px solid #fde68a; color: #92400e;">
            🕒 <strong>Order received.</strong> Canteen staff will start preparing your order soon.
          </div>
        </div>

        <script>
          const orderId = <?php echo (int)$order_id; ?>;
          
          function updateVisualTracker(status) {
            const line = document.getElementById("progress-line");
            const stepPending = document.getElementById("step-pending");
            const stepPreparing = document.getElementById("step-preparing");
            const stepReady = document.getElementById("step-ready");
            
            const pendingAlert = document.getElementById("pending-alert");
            const preparingAlert = document.getElementById("preparing-alert");
            const readyCelebration = document.getElementById("ready-celebration");

            stepPending.className = "step-node";
            stepPreparing.className = "step-node";
            stepReady.className = "step-node";
            
            pendingAlert.style.display = "none";
            preparingAlert.style.display = "none";
            readyCelebration.style.display = "none";

            document.getElementById("lbl-status").innerText = status;

            const isMobile = window.innerWidth <= 768;

            if (status === "Pending") {
              stepPending.className = "step-node active";
              if (isMobile) {
                line.style.height = "0%";
                line.style.width = "4px";
              } else {
                line.style.width = "0%";
                line.style.height = "4px";
              }
              pendingAlert.style.display = "block";
            } else if (status === "Preparing") {
              stepPending.className = "step-node completed";
              stepPreparing.className = "step-node active";
              if (isMobile) {
                line.style.height = "50%";
                line.style.width = "4px";
              } else {
                line.style.width = "50%";
                line.style.height = "4px";
              }
              preparingAlert.style.display = "block";
            } else if (status === "Ready") {
              stepPending.className = "step-node completed";
              stepPreparing.className = "step-node completed";
              stepReady.className = "step-node completed active";
              if (isMobile) {
                line.style.height = "100%";
                line.style.width = "4px";
              } else {
                line.style.width = "100%";
                line.style.height = "4px";
              }
              readyCelebration.style.display = "block";
            }
          }

          function checkStatus() {
            fetch(`php/get_order_status.php?order_id=${orderId}`)
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  updateVisualTracker(data.status);
                  if (data.status === "Ready") {
                    clearInterval(pollInterval);
                  }
                }
              })
              .catch(err => console.error("Error fetching order status:", err));
          }

          updateVisualTracker("<?php echo $status; ?>");

          let pollInterval;
          if ("<?php echo $status; ?>" !== "Ready") {
            pollInterval = setInterval(checkStatus, 4000);
          }

          window.addEventListener('resize', () => {
             checkStatus();
          });
        </script>
      <?php } ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
