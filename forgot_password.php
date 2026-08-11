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
    $username = trim($_POST['username'] ?? '');
    $passcode = trim($_POST['passcode'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $passcode === '' || $new_password === '') {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif ($passcode !== 'CAMPUSADMIN2026') {
        $error = "Invalid Admin Security Passcode!";
    } else {
        $username_safe = mysqli_real_escape_string($conn, $username);
        $result = mysqli_query($conn, "SELECT id, role FROM users WHERE username = '$username_safe'");
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($user['role'] !== 'admin') {
                $error = "Unauthorized. Only Administrator passwords can be reset here.";
            } else {
                $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $hashed_pw, $user['id']);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Password reset successfully! You can now login.";
                } else {
                    $error = "Failed to update password. Try again.";
                }
            }
        } else {
            $error = "Admin username not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Campus Canteen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            max-width: 420px;
            margin: 60px auto;
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
    </style>
</head>
<body class="auth-body">
    <div class="container" style="max-width: 500px;">
        <div class="auth-container">
            <h2 style="text-align: center; margin-bottom: 25px; color: #2c3e50; font-weight: 700;">Reset Password</h2>
            
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
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Admin Username:</label>
                    <input type="text" name="username" required placeholder="Enter admin username">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #e53e3e;">Admin Security Passcode:</label>
                    <input type="password" name="passcode" required placeholder="Enter passcode to verify reset">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">New Password:</label>
                    <input type="password" name="new_password" required placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Confirm New Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 10px; padding: 12px;">Reset Password</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #4a5568;">
                Back to <a href="login.php" style="color: #0b57d0; font-weight: 700; text-decoration: none;">Login page</a>
            </p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
