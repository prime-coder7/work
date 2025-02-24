<?php
// Include the database connection
include '../components/connected.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['seller_id'];
    $message = $_POST['message'];
    $status = 'unread';

    // Insert the message into the database
    $insert_message = $conn->prepare("INSERT INTO message (sender_id, receiver_id, message, status) VALUES (?, ?, ?, ?)");
    $insert_message->execute([$sender_id, $receiver_id, $message, $status]);

    // Redirect back to the seller's product or message page
    header('Location: view_product.php');
    exit();
}
?>

<!-- HTML Form to send a message -->
<form method="POST">
    <input type="hidden" name="seller_id" value="SELLER_ID_HERE">
    <textarea name="message" placeholder="Type your message here..."></textarea>
    <button type="submit">Send Message</button>
</form>