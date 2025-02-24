<?php
include '../components/connected.php'; // Include database connection

// Check if the user is logged in (either as a user or seller)
if (isset($_SESSION['user_id'])) {
    // User login, fetch user data
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_user->execute([$_SESSION['user_id']]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Check if the role is 'user' or 'admin'
        if ($user['role'] == 'user') {
            header('Location: ../user_pannel/user_dashboard.php'); // Redirect to user dashboard
        } elseif ($user['role'] == 'admin') {
            header('Location: ../admin_pannel/dashboard.php'); // Redirect to admin dashboard
        } else {
            // Fallback case if the role is unknown
            header('Location: ../components/login.php'); // Redirect back to login page (or any error page)
        }
    } else {
        // In case user is not found
        header('Location: ../components/login.php');
    }
    exit();
}

// Check if the seller is logged in
if (isset($_SESSION['seller_id'])) {
    // Seller login, fetch seller data
    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
    $select_seller->execute([$_SESSION['seller_id']]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);
    
    if ($seller) {
        // Check if the role is 'seller'
        if ($seller['role'] == 'seller') {
            header('Location: ../admin_pannel/dashboard.php'); // Redirect to admin panel (for sellers)
        } else {
            // Handle fallback if the role is unexpected
            header('Location: ../components/login.php'); // Redirect to login or error page
        }
    } else {
        // In case seller is not found
        header('Location: ../components/login.php');
    }
    exit();
}

// If neither user nor seller is logged in, redirect to the login page
header('Location: ../components/login.php');
exit();
?>
