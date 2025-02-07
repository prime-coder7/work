<?php
// Start the session to ensure access to the seller's session data
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection (using the same connection from dashboard.php)
include '../components/connected.php'; // Make sure this file has the correct database connection

// Check if the seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit(); // Stop execution if the seller is not logged in
}

// Fetch the seller details from the database
$seller_id = $_SESSION['seller_id'];
$select_seller = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
$select_seller->execute([$seller_id]);
$seller = $select_seller->fetch(PDO::FETCH_ASSOC);

// Update profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? $seller['name'];
    $phone = $_POST['phone'] ?? $seller['phone'];
    $address = $_POST['address'] ?? $seller['address'];

    // Handle profile image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = '../uploaded_files/' . $image_name;
        move_uploaded_file($image_tmp, $image_path);

        // Update the image in the database
        $update_image = $conn->prepare("UPDATE sellers SET image = ? WHERE id = ?");
        $update_image->execute([$image_name, $seller_id]);
    }

    // Update the seller details (name, phone, address)
    $update_seller = $conn->prepare("UPDATE sellers SET name = ?, phone = ?, address = ? WHERE id = ?");
    $update_seller->execute([$name, $phone, $address, $seller_id]);

    // Redirect to the profile page after updating
    header('Location: user_accounts.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Seller Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-content {
            padding: 20px;
            margin-top: 20px;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-header h1 {
            color: red;
        }

        .profile-info {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-info img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .profile-info form {
            width: 100%;
            max-width: 500px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info input[type="text"],
        .profile-info input[type="email"],
        .profile-info input[type="file"],
        .profile-info textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-btn {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-btn:hover {
            background-color: darkred;
        }

        .profile-alert {
            color: green;
            margin: 10px;
        }

        .profile-action-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }

        .action-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <h1>Your Profile</h1>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="profile-alert"><?= htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>

            <div class="profile-info">
                <img src="../uploaded_files/<?= $seller['image'] ?: 'default_image.jpg'; ?>" alt="Profile Image">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($seller['name']); ?>" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($seller['email']); ?>" disabled>

                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($seller['phone'] ?? ''); ?>" required>

                    <label for="address">Address</label>
                    <!-- For the address textarea field -->
                    <textarea id="address" name="address" rows="4" required><?= htmlspecialchars($seller['address'] ?? ''); ?></textarea>
                    
                    <label for="image">Profile Image</label>
                    <input type="file" id="image" name="image" accept="image/*">

                    <button type="submit" class="profile-btn">Update Profile</button>
                </form>
            </div>

            <div class="profile-action-links">
                <a href="dashboard.php" class="action-btn">Back to Home</a>
                <a href="admin_logout.php" class="action-btn">Logout</a>
            </div>
        </div>
    </div>

</body>
</html>
