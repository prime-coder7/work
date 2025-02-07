<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session to log out the admin
session_destroy();

// Optionally, clear cookies if used for login persistence
// setcookie('seller_id', '', time() - 3600, '/'); // Uncomment if you're using cookies

// Redirect to login page after logout
header('Location: login.php');
exit();
?>
