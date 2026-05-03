<?php
include "connect.php";

if (!isset($conn)) {
  if (isset($con)) {
    $conn = $con;
  } elseif (isset($link)) {
    $conn = $link;
  } else {
    die("Database connection not found");
  }
}

$name = trim($_POST['name'] ?? '');
$price = (int)($_POST['price'] ?? 0);

if ($name === '' || $price <= 0) {
  die("Invalid data");
}

$stmt = mysqli_prepare($conn, "INSERT INTO products (name, price) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "si", $name, $price);
mysqli_stmt_execute($stmt);

header("Location: ../admin_products.php");
exit;
