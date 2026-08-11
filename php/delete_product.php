<?php
include "connect.php";
include "auth.php";

require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  die("Invalid ID");
}

// Fetch image path to delete the physical file from server
$res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($res);
if ($product) {
    $image_path = $product['image'];
    if ($image_path !== '' && file_exists('../' . $image_path)) {
        @unlink('../' . $image_path);
    }
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
  
header("Location: ../admin_products.php");
exit;
?>
