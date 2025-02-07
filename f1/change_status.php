<?php
include '../components/connected.php';

// Check if ID and status are provided
if (isset($_GET['id']) && isset($_GET['status'])) {
    $product_id = $_GET['id'];
    $new_status = $_GET['status'];

    // Ensure the status is either 'active' or 'deactive'
    if (in_array($new_status, ['active', 'deactive'])) {
        // Update the product status in the database
        $update_status = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
        $update_status->execute([$new_status, $product_id]);

        // Redirect back to the view products page with a success message
        header('Location: view_product.php?msg=Product+status+updated');
        exit();
    } else {
        // Invalid status
        header('Location: view_product.php?msg=Invalid+status');
        exit();
    }
} else {
    // Invalid request
    header('Location: view_product.php?msg=Invalid+request');
    exit();
}
