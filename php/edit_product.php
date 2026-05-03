<?php
require_once __DIR__ . "/connect.php";

if (!isset($conn)) {
  die("Database connection not found");
}

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = (int)($_POST['price'] ?? 0);

if ($id <= 0 || $name === '' || $price <= 0) {
  die("Invalid data");
}

$stmt = mysqli_prepare($conn, "UPDATE products SET name=?, price=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sii", $name, $price, $id);
mysqli_stmt_execute($stmt);

header("Location: ../admin_products.php");
exit;
?>
