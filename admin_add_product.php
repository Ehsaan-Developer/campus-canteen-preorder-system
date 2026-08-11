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
  <title>Add Food Item | Campus Canteen</title>
  <!-- FontAwesome for professional icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .form-box {
      max-width: 500px;
      margin: 30px auto;
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 30px;
      box-shadow: var(--shadow-md);
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

    <h1 class="page-title"><i class="fa-solid fa-circle-plus"></i> Add Food Item</h1>

    <div class="form-box">
      <form action="php/add_product.php" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap: 15px;">
        <div>
          <label>Product Name:</label>
          <input type="text" name="name" required placeholder="e.g. Chicken Burger">
        </div>

        <div>
          <label>Category:</label>
          <select name="category" required>
            <option value="Fast Food">Fast Food</option>
            <option value="Meals">Meals</option>
            <option value="Beverages">Beverages</option>
            <option value="Snacks">Snacks</option>
          </select>
        </div>

        <div>
          <label>Price (Rs.):</label>
          <input type="number" name="price" required placeholder="e.g. 250">
        </div>

        <div>
          <label>Product Image (Optional):</label>
          <input type="file" name="image" id="image-input" accept="image/*" style="padding: 5px;">
          <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 3px;">Format support: JPG, PNG, WEBP. Max size: 2MB.</span>
          
          <!-- Image Preview Pane -->
          <div id="image-preview" style="margin-top: 15px; display: none;">
            <span style="font-size: 0.85rem; color: var(--text-muted); display:block; margin-bottom: 5px;">Selected Image Preview:</span>
            <img id="preview-img" src="" style="max-height: 120px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover;">
          </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 15px;">
          <a class="btn btn-secondary" href="admin_products.php" style="flex-grow: 1;"><i class="fa-solid fa-xmark"></i> Cancel</a>
          <button type="submit" class="btn" style="flex-grow: 1;"><i class="fa-solid fa-check"></i> Add to Menu</button>
        </div>
      </form>
    </div>
  </div>
  
  <?php include 'footer.php'; ?>

  <script>
    document.getElementById('image-input').addEventListener('change', function() {
        const file = this.files[0];
        const previewContainer = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (file) {
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert("⚠️ Error: This photo is too large (" + (file.size / 1024 / 1024).toFixed(1) + "MB).\n\nStandard PHP limits uploads to 2MB. Please select a smaller photo or compress it first!");
                this.value = ''; // Reset file input
                previewContainer.style.display = 'none';
                return;
            }
            
            // Show Image Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
  </script>
</body>
</html>