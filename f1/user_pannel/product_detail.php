<?php
session_start();  // Start the session

// Include the database connection
include('../components/connected.php');

// Redirect if the seller is not logged in (if needed)
if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit();
}

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details from the database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product exists
    if (!$product) {
        header('Location: product_list.php');
        exit();
    }
} else {
    header('Location: product_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .product-detail {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .product-image {
            flex: 1;
            margin-right: 20px;
        }

        .product-image img {
            width: auto;
            max-width: 500px;
            height: auto;
            border-radius: 8px;
        }

        .product-info {
            flex: 1;
        }

        .product-info h1 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #333;
        }

        .product-info p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .product-info .price {
            font-size: 24px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 20px;
        }

        .product-info .buy-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .product-info .buy-button:hover {
            background-color: #2980b9;
        }

        .back-link {
            margin-top: 20px;
            font-size: 16px;
        }

        .back-link a {
            text-decoration: none;
            color: #3498db;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Details</h1>
        <div class="product-detail">
            <div class="product-image">
                <img src="../uploaded_files/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="price">$<?php echo $product['price']; ?></p>
                <a href="cart.php?add=<?php echo $product['id']; ?>" class="buy-button">Add to Cart</a>
                <p class="back-link"><a href="product_list.php">Back to Product List</a></p>
            </div>
        </div>
    </div>
</body>
</html>
