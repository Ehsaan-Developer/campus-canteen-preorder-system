<?php
include "connect.php";
include "auth.php";

require_admin();

$name = trim($_POST['name'] ?? '');
$price = (int)($_POST['price'] ?? 0);
$category = trim($_POST['category'] ?? 'Fast Food');
$image_path = "";

if ($name === '' || $price <= 0) {
  die("Invalid data");
}

// Handle Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = 'prod_' . time() . '_' . md5(uniqid()) . '.' . $fileExtension;
        $uploadFileDir = '../uploads/';
        $dest_path = $uploadFileDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_path = 'uploads/' . $newFileName;
        }
    }
}

$stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, category, image) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "siss", $name, $price, $category, $image_path);
mysqli_stmt_execute($stmt);

header("Location: ../admin_products.php");
exit;
?>
