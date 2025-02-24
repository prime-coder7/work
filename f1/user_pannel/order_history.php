<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_user.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Get past orders for this user
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
</head>
<body>
    <h1>Order History</h1>
    <?php foreach ($orders as $order): ?>
        <div>
            <p>Order ID: <?php echo $order['id']; ?> - Status: <?php echo $order['status']; ?> - Total Price: $<?php echo $order['total_price']; ?></p>
            <p>Order Date: <?php echo $order['order_date']; ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
