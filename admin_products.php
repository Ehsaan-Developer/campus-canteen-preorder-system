<?php
include "php/connect.php";
include "php/auth.php";

require_admin();

$res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Canteen Inventory | Campus Canteen</title>
  <!-- FontAwesome for professional icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }
    .product-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 20px;
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
    }
    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: var(--radius-sm);
      margin-bottom: 12px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Admin navbar with FontAwesome icons -->
    <div class="navbar" style="border-left: 5px solid var(--primary);">
        <div class="nav-left">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="admin_orders.php"><i class="fa-solid fa-list-check"></i> Manage Orders</a>
            <a href="admin_products.php" class="active"><i class="fa-solid fa-utensils"></i> Canteen Menu Items</a>
            <a href="analysis.php"><i class="fa-solid fa-chart-simple"></i> Sales Report</a>
            <a href="admin_register.php"><i class="fa-solid fa-clock-rotate-left"></i> Shift Register</a>
            <a href="menu.php" target="_blank"><i class="fa-solid fa-eye"></i> Customer View</a>
        </div>
        <div>
            <a href="logout.php" class="btn btn-danger" style="padding: 8px 15px; font-size: 0.9rem;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-family: Outfit; font-size: 2rem;"><i class="fa-solid fa-boxes-stacked"></i> Canteen Menu Inventory</h1>
        <a class="btn" href="admin_add_product.php"><i class="fa-solid fa-plus"></i> Add New Product</a>
    </div>

    <div class="product-grid">
      <?php if ($res && mysqli_num_rows($res) > 0) { 
        while($p = mysqli_fetch_assoc($res)) { 
          $img = $p['image'] ?? '';
          $cat = $p['category'] ?? 'Fast Food';
          ?>
          <div class="product-card">
            <?php if ($img !== '' && file_exists(__DIR__ . '/' . $img)) { ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
            <?php } else { ?>
                <div style="height: 150px; background: linear-gradient(135deg, #ffe5d9 0%, #fff0eb 100%); display: flex; align-items: center; justify-content: center; font-size: 3rem; border-radius: var(--radius-sm); margin-bottom: 12px; color: var(--primary);"><i class="fa-solid fa-bowl-food"></i></div>
            <?php } ?>
            
            <span class="badge" style="background: rgba(242, 100, 25, 0.08); color: var(--primary); font-size: 0.75rem; width: fit-content; margin-bottom: 8px; border-radius: 4px; padding: 3px 8px; font-weight: 600; text-transform: uppercase;">
                <?php echo htmlspecialchars($cat); ?>
            </span>
            
            <h3 style="font-family: Outfit; font-size: 1.25rem; margin-bottom: 5px;"><?php echo htmlspecialchars($p['name']); ?></h3>
            <p style="font-weight: 700; color: var(--primary); font-size: 1.15rem; margin-bottom: 15px;">Rs. <?php echo (int)$p['price']; ?></p>
            
            <div style="display: flex; gap: 8px; margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 15px;">
              <a href="admin_edit_product.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-secondary" style="flex-grow: 1; padding: 8px 12px; font-size: 0.9rem;"><i class="fa-solid fa-pen"></i> Edit</a>
              <a href="php/delete_product.php?id=<?php echo (int)$p['id']; ?>" 
                 onclick="return confirm('Are you sure you want to delete this food item?');" 
                 class="btn btn-danger" style="padding: 8px 12px; font-size: 0.9rem;"><i class="fa-solid fa-trash-can"></i> Delete</a>
            </div>
          </div>
        <?php } 
      } else { ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #fff; border-radius: 12px; border: 1px solid var(--border-color); color: var(--text-muted);">
            No products in the menu yet. Click "+ Add New Product" to populate the canteen menu.
        </div>
      <?php } ?>
    </div>
  </div>
  
  <?php include 'footer.php'; ?>
</body>
</html>
