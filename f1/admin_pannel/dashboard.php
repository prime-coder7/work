<?php
// Start the session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../components/connected.php'; // Include database connection

// Check if the seller is logged in using the session
if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['seller_id']; // Get the logged-in seller's ID

// Fetch seller profile details
$select_profile = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
$select_profile->execute([$seller_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Get product count
$select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
$select_products->execute([$seller_id]);
$number_of_products = $select_products->rowCount();

// Get order count
$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ?");
$select_orders->execute([$seller_id]);
$number_of_orders = $select_orders->rowCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formula 1 - Seller Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* Red theme for profile box */
        .profile-box {
            flex: 1;
            padding: 20px;
            background-color: #f8d7da; /* Light red */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-box h2 {
            color: #721c24; /* Dark red for text */
        }
        .profile-box p {
            color: #721c24;
        }
        .profile-box a {
            background-color: #dc3545; /* Red button */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .profile-box a:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="main-container">
    <?php include '../components/admin_header.php'; ?>

    <section class="dashboard">
        <div class="heading">
            <h1>Welcome to the Dashboard</h1>
            <img src="https://tse4.mm.bing.net/th?id=OIP.zgnLbe1w5yJKtr-_Nbf-hwHaHa&pid=Api&P=0&h=180" alt="Dashboard Image">
        </div>

        <div class="box-container">
            <!-- Profile Info Box with Red Theme -->
            <div class="box profile-box">
                <?php
                // Handle image path check
                $profile_image = $fetch_profile['profile_image'] ?? 'default.jpg';
                $image_path = "../uploaded_files/" . $profile_image;

                // Check if the file exists
                if (!file_exists($image_path)) {
                    $profile_image = 'default.jpg'; // Fallback to default image if not found
                }
                ?>

                <img src="../uploaded_files/<?= $seller['image'] ?: 'default_image.jpg'; ?>" alt="Profile Image">
                <h2>Welcome, <?= htmlspecialchars($fetch_profile['name'] ?? 'Unknown'); ?>!</h2>
                <p><strong>Email:</strong> <?= htmlspecialchars($fetch_profile['email'] ?? 'Not Provided'); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($fetch_profile['phone'] ?? 'Not Provided'); ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($fetch_profile['location'] ?? 'Not Provided'); ?></p>
                <a href="user_accounts.php" class="btn">Update Profile</a>
            </div>

            <!-- Unread Messages -->
            <div class="box">
                <?php
                $select_message = $conn->prepare("SELECT * FROM `message`");
                $select_message->execute();
                $number_of_msg = $select_message->rowCount();
                ?>
                <h3><?= $number_of_msg; ?></h3>
                <p>Unread Messages</p>
                <a href="admin_message.php" class="btn">See Messages</a>
            </div>

            <!-- Products Added -->
            <div class="box">
                <h3><?= $number_of_products; ?></h3>
                <p>Products Added</p>
                <a href="add_product.php" class="btn">Add Product</a>
            </div>

            <!-- Active Products -->
            <div class="box">
                <?php
                // Fetch active products count for the seller
                $select_active_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'active'");
                $select_active_products->execute([$seller_id]);
                $number_of_active_products = $select_active_products->rowCount();
                ?>
                <h3><?= $number_of_active_products; ?></h3>
                <p>Active Products</p>
                <a href="view_product.php" class="btn">View Active Products</a>
            </div>

            <!-- Deactive Products -->
            <div class="box">
                <?php
                $select_deactive_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'deactive'");
                $select_deactive_products->execute([$seller_id]);
                $number_of_deactive_products = $select_deactive_products->rowCount();
                ?>
                <h3><?= $number_of_deactive_products; ?></h3>
                <p>Deactive Products</p>
                <a href="view_product.php" class="btn">View Deactive Products</a>
            </div>

            <!-- Users Account -->
            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users`");
                $select_users->execute();
                $number_of_users = $select_users->rowCount();
                ?>
                <h3><?= $number_of_users; ?></h3>
                <p>Users Accounts</p>
                <a href="user_accounts.php" class="btn">View Users</a>
            </div>

            <!-- Sellers Account -->
            <div class="box">
                <?php
                $select_sellers = $conn->prepare("SELECT * FROM `sellers`");
                $select_sellers->execute();
                $number_of_sellers = $select_sellers->rowCount();
                ?>
                <h3><?= $number_of_sellers; ?></h3>
                <p>Sellers Accounts</p>
                <a href="user_accounts.php" class="btn">View Sellers</a>
            </div>

            <!-- Total Orders -->
            <div class="box">
                <h3><?= $number_of_orders; ?></h3>
                <p>Total Orders</p>
                <a href="admin_order.php" class="btn">View Orders</a>
            </div>

            <!-- Confirm Orders -->
            <div class="box">
                <?php
                $select_confirm_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ? AND status = 'in progress'");
                $select_confirm_orders->execute([$seller_id]);
                $number_of_confirm_orders = $select_confirm_orders->rowCount();
                ?>
                <h3><?= $number_of_confirm_orders; ?></h3>
                <p>Confirm Orders</p>
                <a href="admin_order.php" class="btn">View Confirmed Orders</a>
            </div>

            <!-- Canceled Orders -->
            <div class="box">
                <?php
                $select_canceled_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ? AND status = 'canceled'");
                $select_canceled_orders->execute([$seller_id]);
                $number_of_canceled_orders = $select_canceled_orders->rowCount();
                ?>
                <h3><?= $number_of_canceled_orders; ?></h3>
                <p>Canceled Orders</p>
                <a href="admin_order.php" class="btn">View Canceled Orders</a>
            </div>
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>
</body>
</html>
