<?php
include "php/connect.php";
include "php/auth.php";

if (is_admin()) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";
$success = "";

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $existing_user = trim($_POST['existing_admin_user'] ?? '');
    $existing_pass = $_POST['existing_admin_pass'] ?? '';

    if ($new_username === '' || $new_password === '' || $existing_user === '' || $existing_pass === '') {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        $existing_user_safe = mysqli_real_escape_string($conn, $existing_user);
        $check_existing = mysqli_query($conn, "SELECT * FROM users WHERE username = '$existing_user_safe' AND role = 'admin'");
        
        if ($check_existing && mysqli_num_rows($check_existing) > 0) {
            $old_admin = mysqli_fetch_assoc($check_existing);
            
            if (password_verify($existing_pass, $old_admin['password'])) {
                $new_username_safe = mysqli_real_escape_string($conn, $new_username);
                $check_duplicate = mysqli_query($conn, "SELECT id FROM users WHERE username = '$new_username_safe'");
                
                if (mysqli_num_rows($check_duplicate) > 0) {
                    $error = "New username already exists!";
                } else {
                    $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
                    mysqli_stmt_bind_param($stmt, "ss", $new_username, $hashed_pw);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "New Admin account registered successfully! You can now login.";
                    } else {
                        $error = "Registration failed. Try again.";
                    }
                }
            } else {
                $error = "Authentication failed! Incorrect password for existing administrator.";
            }
        } else {
            $error = "Authentication failed! Existing admin username not found or unauthorized.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | Campus Canteen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            max-width: 450px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .auth-form input {
            width: 100%;
            box-sizing: border-box;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .section-separator {
            border-top: 1px solid var(--border-color);
            margin: 15px 0 5px 0;
            padding-top: 15px;
        }
    </style>
</head>
<body class="auth-body">
    <div class="container" style="max-width: 550px;">
        <div class="auth-container">
            <h2 style="text-align: center; margin-bottom: 25px; color: #2c3e50; font-weight: 700;">Admin Sign Up</h2>
            
            <?php if ($error !== "") { ?>
                <div class="alert alert-danger" style="color: #d93025; background: #fde8e8; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; border: 1px solid #f8b4b4; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <?php if ($success !== "") { ?>
                <div class="alert alert-success" style="color: #137333; background: #e6f4ea; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; border: 1px solid #c2e7c9; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($success); ?>
                    <p style="margin: 8px 0 0;"><a href="login.php" class="btn" style="display: inline-block; text-decoration: none; padding: 6px 12px; font-size: 0.85rem;">Login here</a></p>
                </div>
            <?php } ?>

            <?php if ($success === "") { ?>
            <form class="auth-form" method="POST" action="">
                <!-- New Admin Details -->
                <h3 style="font-family: Outfit; font-size: 1.1rem; color: var(--primary); margin-bottom: 5px;">New Admin Credentials</h3>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">New Admin Username:</label>
                    <input type="text" name="username" required placeholder="Choose new username">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">New Admin Password:</label>
                    <input type="password" name="password" required placeholder="Enter secure password">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Confirm Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                
                <!-- Existing Admin Verification -->
                <div class="section-separator">
                    <h3 style="font-family: Outfit; font-size: 1.1rem; color: #e53e3e; margin-bottom: 5px;">Existing Admin Authorization</h3>
                    <span style="font-size:0.8rem; color: var(--text-muted); display:block; margin-bottom:10px;">To register a new administrator, you must verify the credentials of an active admin account.</span>
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Active Admin Username:</label>
                    <input type="text" name="existing_admin_user" required placeholder="Enter current admin username">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Active Admin Password:</label>
                    <input type="password" name="existing_admin_pass" required placeholder="Enter current admin password">
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 10px; padding: 12px;">Create Admin Account</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #4a5568;">
                Already have an Admin account? <a href="login.php" style="color: #0b57d0; font-weight: 700; text-decoration: none;">Login here</a>
            </p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
