<?php
include "connect.php";
include "auth.php";

require_admin();

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = (int)($_POST['price'] ?? 0);
$category = trim($_POST['category'] ?? 'Fast Food');

if ($id <= 0 || $name === '' || $price <= 0) {
  die("Invalid data");
}

// Fetch current product details to preserve image if no new one uploaded
$res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($res);
if (!$product) {
    die("Product not found");
}

$image_path = $product['image'];

// Handle New Image Upload
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
            // Delete old file if exists
            if ($image_path !== '' && file_exists('../' . $image_path)) {
                @unlink('../' . $image_path);
            }
            $image_path = 'uploads/' . $newFileName;
        }
    }
}

$stmt = mysqli_prepare($conn, "UPDATE products SET name=?, price=?, category=?, image=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sissi", $name, $price, $category, $image_path, $id);
mysqli_stmt_execute($stmt);

header("Location: ../admin_products.php");
exit;
?>
