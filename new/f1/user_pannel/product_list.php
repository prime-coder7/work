<?php
session_start();  // Start the session

// Include the database connection
include('../components/connected.php');

// Redirect if the seller is not logged in
if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit();
}

// Check if the user is a seller
if (isset($_SESSION['seller_id'])) {
    // Fetch the seller's details
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
    $select_seller->execute([$_SESSION['seller_id']]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    // Redirect seller to the dashboard
    if ($seller['role'] == 'seller') {
        header('Location: ../admin_pannel/dashboard.php');
        exit();
    }
}

// Fetch all active products from the database for users
$stmt = $conn->prepare("SELECT * FROM products WHERE status = 'active'");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
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

h1 {
    text-align: center;
    color: #333;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.product-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.product-card:hover {
    transform: scale(1.05);
}

/* Uniform size for product images */
.product-card img {
    width: 100%;
    height: 200px; /* Set a fixed height */
    object-fit: contain; /* Ensures the image fills the area without distortion */
    background-color: #f0f0f0; /* Fallback background color */
    border-bottom: 1px solid #ddd;
}

.product-card .product-info {
    padding: 15px;
}

.product-card h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}

.product-card p {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
}

.product-card .price {
    font-size: 18px;
    font-weight: bold;
    color: #e67e22;
    margin-bottom: 15px;
}

.product-card a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.product-card a:hover {
    background-color: #2980b9;
}

.no-products {
    text-align: center;
    font-size: 18px;
    color: #e74c3c;
    margin-top: 20px;
}
</style>
</head>
<body>
    <div class="container">
        
        <h1>Product List</h1>

        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="../uploaded_files/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-products">No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>

