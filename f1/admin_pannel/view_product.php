<?php
include '../components/connected.php';

// Ensure the seller is logged in via session (session should take priority)
if (!isset($_SESSION['seller_id']) && !isset($_COOKIE['seller_id'])) {
    header('Location: login.php');
    exit();
}

// Set the seller_id either from session or cookie
$seller_id = $_SESSION['seller_id'] ?? $_COOKIE['seller_id'];

// Fetch active products for this seller
$select_active_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'active'");
$select_active_products->execute([$seller_id]);
$active_products = $select_active_products->fetchAll(PDO::FETCH_ASSOC);

// Fetch deactivated products for this seller
$select_inactive_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'inactive'");
$select_inactive_products->execute([$seller_id]);
$inactive_products = $select_inactive_products->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert"><?= htmlspecialchars($_GET['msg'] ?? ''); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Products - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .main-container {
            /* max-width: 1200px; */
            margin: 0 0;
            padding: 20px;
        }

        .view-products {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .heading {
            text-align: center;
            margin-bottom: 20px;
            color: #d93a3a;
        }

        .heading h1 {
            font-size: 2.5em;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .product-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 10px;
            background-color: #f9f9f9;
            width: 280px;
            height: auto; /* Ensure the box grows as needed */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Add space between content and button */
            box-sizing: border-box;
            border-radius: 8px;
        }

        .product-box:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .product-box img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .product-box h3 {
            font-size: 1.2em;
            color: #333;
            margin: 10px 0;
        }

        .product-box p {
            font-size: 1em;
            color: #555;
            margin: 5px 0;
        }

        .product-box .btn {
            background-color: #d93a3a;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            margin-top: 20px; /* Space above the button */
            cursor: pointer;
            font-size: 1em;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-align: center; /* Ensure text is centered */
        }

        .product-box .btn:hover {
            background-color: #b53131;
        }

        .alert {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <?php include '../components/admin_header.php'; ?>

        <section class="view-products">
            <div class="heading">
                <h1>Active Products</h1>
            </div>
            <div class="product-container">
                <?php if (count($active_products) > 0): ?>
                    <?php foreach ($active_products as $product): ?>
                        <div class="product-box">
                            <img src="../uploaded_files/<?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'default_image.jpg'; ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product'); ?>" />
                            <h3><?= htmlspecialchars($product['name'] ?? 'Unknown Product'); ?></h3>
                            <p><?= htmlspecialchars($product['description'] ?? 'No description available'); ?></p>
                            <p>Price: $<?= htmlspecialchars($product['price'] ?? 0); ?></p>
                            <p>Status: <?= htmlspecialchars($product['status'] ?? 'Not Available'); ?></p>
                            <a href="update_product.php?product_id=<?= urlencode($product['id']); ?>" class="btn">Update</a>
                            <a href="change_status.php?id=<?= urlencode($product['id']); ?>&status=inactive" class="btn" onclick="return confirm('Are you sure you want to deactivate this product?');">Deactivate</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No active products found.</p>
                <?php endif; ?>
            </div>

            <div class="heading">
                <h1>Deactivated Products</h1>
            </div>
            <div class="product-container">
                <?php if (count($inactive_products) > 0): ?>
                    <?php foreach ($inactive_products as $product): ?>
                        <div class="product-box">
                            <img src="../uploaded_files/<?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'default_image.jpg'; ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product'); ?>" />
                            <h3><?= htmlspecialchars($product['name'] ?? 'Unknown Product'); ?></h3>
                            <p><?= htmlspecialchars($product['description'] ?? 'No description available'); ?></p>
                            <p>Price: $<?= htmlspecialchars($product['price'] ?? 0); ?></p>
                            <p>Status: <?= htmlspecialchars($product['status'] ?? 'Not Available'); ?></p>
                            <a href="update_product.php?product_id=<?= urlencode($product['id']); ?>" class="btn">Update</a>
                            <a href="change_status.php?id=<?= urlencode($product['id']); ?>&status=active" class="btn" onclick="return confirm('Are you sure you want to activate this product?');">Activate</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No deactivated products found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>