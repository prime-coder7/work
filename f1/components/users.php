<?php
// Start the session and check if the seller is logged in
session_start();
include '../components/connected.php'; // Include database connection

// Check if the seller is logged in using the session
if (!isset($_SESSION['seller_id'])) {
    header('Location: ../components/login.php');
    exit();
}

$seller_id = $_SESSION['seller_id']; // Get the logged-in seller's ID

// Fetch all users (viewers or general users) from the 'users' table
$select_users = $conn->prepare("SELECT * FROM `users`");
$select_users->execute();
$users = $select_users->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Users - Admin</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* Custom styling for user listing */
        .user-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .user-box {
            width: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .user-box:hover {
            background-color: #e9ecef;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .user-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .user-box h3 {
            color: #343a40;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .user-box p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .user-box .btn {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .user-box .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="main-container">
    <?php include '../components/admin_header.php'; ?>

    <section class="dashboard">
        <div class="heading">
            <h1>View Users</h1>
            <p>Here is the list of all users</p>
        </div>

        <div class="user-list-container">
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-box">
                        <?php
                        // Check if the user has a profile image, otherwise use a default image
                        $user_image = !empty($user['image']) ? $user['image'] : 'default_image.jpg';
                        $image_path = "../uploaded_files/" . $user_image;

                        // If the image doesn't exist, set to default
                        if (!file_exists($image_path)) {
                            $image_path = "../uploaded_files/default_image.jpg";
                        }
                        ?>
                        <img src="<?= htmlspecialchars($image_path); ?>" alt="User Image">
                        <h3><?= htmlspecialchars($user['name']); ?></h3>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']); ?></p>
                        <a href="view_user_details.php?id=<?= $user['id']; ?>" class="btn">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>
</body>
</html>
