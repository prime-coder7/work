<?php
session_start();
session_unset();
session_destroy();

// Optionally, clear cookies as well
setcookie('seller_id', '', time() - 3600, '/');

// Redirect to login page after logout
header('Location: ../components/login.php');
exit();
?>
