<?php
include "connect.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  die("Invalid ID");
}

if (isset($conn)) {
  $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}
  
header("Location: ../admin_products.php");
exit;
?>
