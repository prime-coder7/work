<?php
session_start();
include '../components/connected.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: ../components/login.php');
    exit();
}

$seller_id = $_SESSION['seller_id'];

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ?");
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head><title>Order Management</title></head>
<body>
    <h2>Manage Your Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id']; ?></td>
                <td><?= $order['product_name']; ?></td>
                <td><?= $order['status']; ?></td>
                <td><a href="view_order.php?id=<?= $order['id']; ?>">View Order</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
