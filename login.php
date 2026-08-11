<?php
include "php/connect.php";
include "php/auth.php";

if (is_admin()) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please fill in all fields.";
    } else {
        $username_safe = mysqli_real_escape_string($conn, $username);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_safe'");
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: menu.php");
                }
                exit;
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Username not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Campus Canteen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 80px auto;
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
            <h2 style="text-align: center; margin-bottom: 25px; color: #2c3e50; font-weight: 700;">Sign In</h2>
            
            <?php if ($error !== "") { ?>
                <div class="alert alert-danger" style="color: #d93025; background: #fde8e8; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; border: 1px solid #f8b4b4; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Username:</label>
                    <input type="text" name="username" required placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;">Password:</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 10px; padding: 12px;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #4a5568; display: flex; flex-direction: column; gap: 8px;">
                <a href="forgot_password.php" style="color: #64748b; text-decoration: none; font-weight: 600;">Forgot Password?</a>
                <span>New Administrator? <a href="register.php" style="color: #0b57d0; font-weight: 700; text-decoration: none;">Create account</a></span>
            </p>
        </div>
    </div>
</body>
</html>
