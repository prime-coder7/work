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
    header('Location: profile.php');
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .profile-container {
            background-color: #fff;
            padding: 10px; /* Reduced padding */
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Reduced max-width */
            overflow: hidden;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 12px; /* Reduced margin */
        }

        .profile-header h1 {
            color: #e63946;
            font-size: 1.5rem; /* Reduced font size */
            margin-bottom: 6px; /* Reduced margin */
        }

        .profile-header p {
            font-size: 0.85rem; /* Reduced font size */
            color: #888;
        }

        .profile-info {
            width: 100%;
            text-align: center;
        }

        .profile-info img {
            width: 80px; /* Reduced size */
            height: 80px; /* Reduced size */
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e63946; /* Red border around profile picture */
            margin-bottom: 12px; /* Reduced margin */
        }

        .profile-info form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Reduced gap */
            align-items: center;
        }

        /* Styling for input fields */
        .profile-info input[type="text"],
        .profile-info input[type="email"],
        .profile-info input[type="file"],
        .profile-info textarea {
            padding: 8px; /* Reduced padding */
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 0.85rem; /* Reduced font size */
            width: 100%;
            max-width: 350px; /* Reduced max-width */
            margin-bottom: 6px; /* Reduced margin */
            transition: border-color 0.3s ease;
        }

        .profile-info input[type="text"]:focus,
        .profile-info input[type="email"]:focus,
        .profile-info textarea:focus {
            border-color: #e63946;
        }

        /* Label Styling */
        .profile-info label {
            font-size: 0.85rem; /* Reduced font size */
            color: #333;
            margin-bottom: 4px;
            display: block;
            text-align: left;
            width: 100%;
            max-width: 350px;
        }

        .profile-btn {
            background-color: #e63946;
            color: white;
            padding: 8px 16px; /* Reduced padding */
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem; /* Reduced font size */
            transition: background-color 0.3s ease;
            width: 100%;
            max-width: 350px; /* Reduced max-width */
        }

        .profile-btn:hover {
            background-color: #d62828;
        }

        .profile-action-links {
            display: flex;
            justify-content: center;
            margin-top: 14px; /* Reduced margin */
            gap: 8px; /* Reduced gap */
        }

        .action-btn {
            padding: 6px 14px; /* Reduced padding */
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem; /* Reduced font size */
            width: 100px; /* Reduced width */
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .action-btn:hover {
            background-color: #0056b3;
        }

        .action-btn:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <!-- <div class="profile-header">
            <h1>Your Profile</h1>
            <p>Update your details and image.</p>
        </div> -->

        <?php if (isset($_GET['msg'])): ?>
            <div class="profile-alert"><?= htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="profile-info">
            <img src="../uploaded_files/<?= $seller['image'] ?: 'default_image.jpg'; ?>" alt="Profile Image">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($seller['name']); ?>" placeholder="Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($seller['email']); ?>" disabled>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($seller['phone'] ?? ''); ?>" placeholder="Phone" required>

                <label for="address">Address</label>
                <textarea id="address" name="address" rows="2" placeholder="Address" required><?= htmlspecialchars($seller['address'] ?? ''); ?></textarea>

                <label for="image">Profile Image</label>
                <input type="file" id="image" name="image" accept="image/*">

                <button type="submit" class="profile-btn">Update Profile</button>
            </form>
        </div>

        <div class="profile-action-links">
            <a href="dashboard.php" class="action-btn">Back to Home</a>
            <a href="logout.php" class="action-btn">Logout</a>
        </div>
    </div>

</body>
</html>
