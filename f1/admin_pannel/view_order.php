<?php
session_start();
include '../components/connected.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: ../components/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<html>
<head><title>Order Details</title></head>
<body>
    <h2>Order Details</h2>
    <p>Order ID: <?= $order['id']; ?></p>
    <p>Product: <?= $order['product_name']; ?></p>
    <p>Status: <?= $order['status']; ?></p>
    <p>Customer: <?= $order['customer_name']; ?></p>
    <p>Address: <?= $order['customer_address']; ?></p>
    <a href="admin_order.php">Back to Orders</a>
</body>
</html>