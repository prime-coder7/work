<?php
session_start();
include('../components/connected.php');

// If the cart is empty, redirect to the cart page
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];  // Assuming the user is logged in
$seller_id = 1;  // Assuming we link all orders to the same seller for simplicity

// Calculate total price
$total_price = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $total_price += $product['price'] * $quantity;
    }
}

// Process order on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (seller_id, user_id, total_price, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$seller_id, $user_id, $total_price]);

    $order_id = $conn->lastInsertId();  // Get the order ID

    // Insert order items
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $quantity, $product['price'] * $quantity]);
        }
    }

    // Clear the cart after checkout
    unset($_SESSION['cart']);

    // Redirect to order confirmation page
    header('Location: order_confirmation.php?order_id=' . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* CSS for checkout page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .order-summary {
            margin-top: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .order-item-info {
            flex: 1;
            padding-left: 20px;
        }

        .order-item-info h3 {
            margin: 0;
        }

        .order-item-info p {
            font-size: 16px;
            color: #555;
        }

        .total-price {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }

        .submit-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>

        <div class="order-summary">
            <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                <?php
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="order-item">
                    <img src="../uploaded_files/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                    <div class="order-item-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Quantity: <?php echo $quantity; ?></p>
                        <p>Price: $<?php echo $product['price']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-price">
                Total Price: $<?php echo $total_price; ?>
            </div>
        </div>

        <form method="POST">
            <button type="submit" class="submit-btn">Place Order</button>
        </form>
    </div>
</body>
</html>
