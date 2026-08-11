<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin() {
    if (!is_admin()) {
        header("Location: login.php");
        exit;
    }
}
?>
