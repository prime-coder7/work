<?php
session_start();
include('../components/connected.php');

// Function to fetch cart items from the session
$cart_items = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Fetch product details
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];

            $total_price += $product['price'] * $quantity;
        }
    }
}

// Remove product from cart
if (isset($_GET['remove_from_cart'])) {
    $product_id = $_GET['remove_from_cart'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        /* CSS for cart page */
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

        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
            padding-left: 20px;
        }

        .cart-item-info h3 {
            margin: 0;
        }

        .cart-item-info p {
            font-size: 16px;
            color: #555;
        }

        .cart-item-info .quantity {
            font-weight: bold;
        }

        .cart-item-info .price {
            font-weight: bold;
            color: #e67e22;
        }

        .total-price {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }

        .checkout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .checkout-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>

        <?php if (count($cart_items) > 0): ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="../uploaded_files/<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="quantity">Quantity: <?php echo $item['quantity']; ?></p>
                            <p class="price">$<?php echo $item['price']; ?> x <?php echo $item['quantity']; ?> = $<?php echo $item['price'] * $item['quantity']; ?></p>
                        </div>
                        <a href="cart.php?remove_from_cart=<?php echo $item['id']; ?>">Remove</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-price">
                Total Price: $<?php echo $total_price; ?>
            </div>

            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
