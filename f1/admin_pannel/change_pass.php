<?php
// Start session
session_start();

include '../components/connected.php'; // Ensure that your DB connection is included

// Check if the seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['seller_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve current password and new password
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Fetch the current password from the database for the logged-in seller
    $select_seller = $conn->prepare("SELECT password FROM sellers WHERE id = ?");
    $select_seller->execute([$seller_id]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    // Check if the current password matches the stored one (hashed)
    if (password_verify($old_password, $seller['password'])) {
        // Hash the new password
        $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the seller's password in the database
        $update_password = $conn->prepare("UPDATE sellers SET password = ? WHERE id = ?");
        $update_password->execute([$hashed_new_password, $seller_id]);

        echo "Password updated successfully!";
    } else {
        echo "Current password is incorrect.";
    }
}
?>

<!-- Form for changing password -->
<form method="POST">
    <label for="old_password">Current Password:</label>
    <input type="password" name="old_password" id="old_password" required>

    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" id="new_password" required>

    <button type="submit">Change Password</button>
</form>