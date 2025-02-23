<?php
include '../components/connected.php';

// Ensure the seller is logged in via session (session should take priority)
if (!isset($_SESSION['seller_id']) && !isset($_COOKIE['seller_id'])) {
    header('Location: login.php');
    exit();
}

// Set the seller_id either from session or cookie
$seller_id = $_SESSION['seller_id'] ?? $_COOKIE['seller_id'];

// Ensure the product ID and status are provided
if (isset($_GET['id']) && isset($_GET['status'])) {
    $product_id = $_GET['id'];
    $new_status = $_GET['status'];

    // Ensure status is valid
    if (in_array($new_status, ['active', 'inactive'])) {
        // Update product status in the database
        $update_status = $conn->prepare("UPDATE products SET status = ? WHERE id = ? AND seller_id = ?");
        $update_status->execute([$new_status, $product_id, $seller_id]);

        // Redirect back with success message
        header('Location: view_product.php?msg=Product+status+updated');
        exit();
    } else {
        header('Location: view_product.php?msg=Invalid+status');
        exit();
    }
} else {
    header('Location: view_product.php?msg=Invalid+request');
    exit();
}
?>
