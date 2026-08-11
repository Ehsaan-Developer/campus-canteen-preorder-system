<?php
$conn = mysqli_connect("localhost", "root", "", "canteen_db");

if (!$conn) {
    die("Database connection failed. Please ensure MySQL is running in XAMPP. Error: " . mysqli_connect_error());
}

// Check if 'orders' table exists to decide if we need fresh migration
$orders_exist = false;
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if ($check_table && mysqli_num_rows($check_table) > 0) {
    $orders_exist = true;
}

if (!$orders_exist) {
    // Fresh DB setup: Read and run database/canteen_db.sql
    $sql_path = __DIR__ . '/../database/canteen_db.sql';
    if (file_exists($sql_path)) {
        $sql = file_get_contents($sql_path);
        if (mysqli_multi_query($conn, $sql)) {
            do {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($conn));
        }
    }
} else {
    // Incremental migration for existing databases:
    
    // 1. Check and add 'user_id' in orders table
    $check_user_id = mysqli_query($conn, "SHOW COLUMNS FROM `orders` LIKE 'user_id'");
    if ($check_user_id && mysqli_num_rows($check_user_id) == 0) {
        mysqli_query($conn, "ALTER TABLE `orders` ADD `user_id` int(11) DEFAULT NULL AFTER `order_id`");
    }

    // 2. Check and add 'category' in products table
    $check_category = mysqli_query($conn, "SHOW COLUMNS FROM `products` LIKE 'category'");
    if ($check_category && mysqli_num_rows($check_category) == 0) {
        mysqli_query($conn, "ALTER TABLE `products` ADD `category` varchar(50) NOT NULL DEFAULT 'Fast Food'");
    }

    // 3. Check and create 'users' table
    $check_users = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if ($check_users && mysqli_num_rows($check_users) == 0) {
        $create_users_sql = "CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(100) NOT NULL UNIQUE,
          `password` varchar(255) NOT NULL,
          `role` varchar(20) NOT NULL DEFAULT 'customer',
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (mysqli_query($conn, $create_users_sql)) {
            $admin_pw = password_hash('admin123', PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO `users` (username, password, role) VALUES ('admin', '$admin_pw', 'admin')");
        }
    }
}

// --- DAILY REGISTERS MIGRATIONS (Har din ki starting, closing aur shift timings record tracking) ---

// 1. Create 'daily_registers' table if not exists
$check_registers = mysqli_query($conn, "SHOW TABLES LIKE 'daily_registers'");
if ($check_registers && mysqli_num_rows($check_registers) == 0) {
    $create_registers_sql = "CREATE TABLE `daily_registers` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `closed_at` timestamp NULL DEFAULT NULL,
      `status` varchar(20) NOT NULL DEFAULT 'Open',
      `opened_by` varchar(100) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $create_registers_sql);
}

// 2. Add 'register_id' column to 'orders' table to link orders to active shift
$check_order_reg = mysqli_query($conn, "SHOW COLUMNS FROM `orders` LIKE 'register_id'");
if ($check_order_reg && mysqli_num_rows($check_order_reg) == 0) {
    mysqli_query($conn, "ALTER TABLE `orders` ADD `register_id` int(11) DEFAULT NULL AFTER `user_id`");
}
?>