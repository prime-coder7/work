<?php
ob_start();
session_start();
include('../components/connected.php');

if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])) {
    header('Location: ../components/login.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM products WHERE status = 'active'");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('../components/user_base.php'); ?>

<div class="main-content">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Shop Products</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
                    <img src="../uploaded_files/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top product-image" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"> <?php echo htmlspecialchars($product['name']); ?> </h5>
                        <p class="card-text"> <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?> </p>
                        <p class="card-text"><strong>$<?php echo htmlspecialchars($product['price']); ?></strong></p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php ob_end_flush(); ?>

<style>
    .product-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        height: 200px;
        object-fit: cover;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
    }

    .card-text strong {
        color: #ff5733;
    }

    .btn-primary {
        background-color: #ff5733;
        border-color: #ff5733;
    }

    .btn-primary:hover {
        background-color: #d44f2b;
        border-color: #d44f2b;
    }
</style>
