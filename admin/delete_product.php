<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Get and sanitize ID
if ($product_id <= 0) {
    echo "Invalid product ID.";
    exit; // Or redirect
}

// Delete the product
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header("Location: manage_products.php?message=Product deleted successfully"); // Redirect
exit();
?>