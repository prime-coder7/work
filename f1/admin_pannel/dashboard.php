<?php
// Start the session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../components/connected.php'; // Include database connection

// Check if the seller is logged in using the session
if (!isset($_SESSION['seller_id'])) {
    header('Location: ../components/login.php');
    exit();
}



$select_seller = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
$select_seller->execute([$_SESSION['seller_id']]);
$seller = $select_seller->fetch(PDO::FETCH_ASSOC);

if ($seller['role'] != 'seller') {
    header('Location: ../user_pannel/product_list.php');
    exit();
}

$seller_id = $_SESSION['seller_id']; // Get the logged-in seller's ID

// Fetch seller profile details
$select_profile = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
$select_profile->execute([$seller_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

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
        .profile-box img {
            width: 150px; /* Adjust the size as needed */
            height: 150px; /* Maintain square aspect ratio */
            border-radius: 50%; /* This makes the image circular */
            object-fit: cover; /* Ensures the image fits inside the circle */
            margin-bottom: 20px; /* Adds some space below the image */
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
                // Check if image exists in database
                $image_file = !empty($fetch_profile['image']) ? $fetch_profile['image'] : 'default_image.jpg';
                $image_path = "../uploaded_files/" . $image_file;

                // Check if file exists on server
                if (!file_exists($image_path)) {
                    $image_path = "../uploaded_files/default_image.jpg"; // Use default image if not found
                }
                ?>

                <img src="<?= htmlspecialchars($image_path); ?>" alt="Profile Image">
                <h2>Welcome, <?= htmlspecialchars($fetch_profile['name'] ?? 'Unknown'); ?>!</h2>
                <p><strong>Email:</strong> <?= htmlspecialchars($fetch_profile['email'] ?? 'Not Provided'); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($fetch_profile['phone'] ?? 'Not Provided'); ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($fetch_profile['address'] ?? 'Not Provided'); ?></p>
                <a href="../components/profile.php" class="btn">Update Profile</a>
            </div>

            <!-- Unread Messages -->
            <div class="box">
                <?php
                $select_msg = $conn->prepare("SELECT COUNT(*) AS total_messages From `message` WHERE receiver_id = ?");
                $select_msg->execute([$seller_id]);
                $fetch_msg = $select_msg->fetch(PDO::FETCH_ASSOC);
                $number_of_msg = $fetch_msg['total_messages'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_msg); ?></h3>
                <p>Unread Messages</p>
                <a href="admin_message.php" class="btn">See Messages</a>
            </div>

            <!-- Products Added -->
            <div class="box">
                <?php
                    // Get total product count
                    $select_products = $conn->prepare("SELECT COUNT(*) AS total_products FROM `products` WHERE seller_id = ?");
                    $select_products->execute([$seller_id]);
                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                    $number_of_products = $fetch_products['total_products'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_products); ?></h3>
                <p>Products Added</p>
                <a href="add_product.php" class="btn">Add Product</a>
            </div>

            <!-- Active Products -->
            <div class="box">
                <?php
                    // Get active product count
                    $select_active_products = $conn->prepare("SELECT COUNT(*) AS total_active FROM `products` WHERE seller_id = ? AND status = 'active'");
                    $select_active_products->execute([$seller_id]);
                    $fetch_active_products = $select_active_products->fetch(PDO::FETCH_ASSOC);
                    $number_of_active_products = $fetch_active_products['total_active'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_active_products); ?></h3>
                <p>Active Products</p>
                <a href="view_product.php" class="btn">View Active Products</a>
            </div>

            <!-- Deactive Products -->
            <div class="box">
                <?php
                    // Get inactive product count
                    $select_deactive_products = $conn->prepare("SELECT COUNT(*) AS total_inactive FROM `products` WHERE seller_id = ? AND status = 'inactive'");
                    $select_deactive_products->execute([$seller_id]);
                    $fetch_deactive_products = $select_deactive_products->fetch(PDO::FETCH_ASSOC);
                    $number_of_deactive_products = $fetch_deactive_products['total_inactive'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_deactive_products); ?></h3>
                <p>Deactive Products</p>
                <a href="view_product.php" class="btn">View Deactive Products</a>
            </div>

            <!-- Users Account -->
            <div class="box">
                <?php
                    $select_users = $conn->prepare("SELECT COUNT(*) AS total_users FROM `users`");
                    $select_users->execute();
                    $fetch_users = $select_users->fetch(PDO::FETCH_ASSOC);
                    $number_of_users = $fetch_users['total_users'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_users); ?></h3>
                <p>Users Accounts</p>
                <a href="../components/users.php" class="btn">View Users</a>
            </div>
            
            <!-- Sellers Account -->
            <div class="box">
                <?php
                    // Fetch Sellers Count
                    $select_sellers = $conn->prepare("SELECT COUNT(*) AS total_sellers FROM `sellers`");
                    $select_sellers->execute();
                    $fetch_sellers = $select_sellers->fetch(PDO::FETCH_ASSOC);
                    $number_of_sellers = $fetch_sellers['total_sellers'] ?? 0;
                ?>
                <h3><?= htmlspecialchars($number_of_sellers); ?></h3>
                <p>Sellers Accounts</p>
                <a href="../components/sellers.php" class="btn">View Sellers</a>
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
