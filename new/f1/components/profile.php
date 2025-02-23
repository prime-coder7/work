<?php
// Start the session to ensure access to the user's or seller's session data
session_start();

// Include the database connection
include '../components/connected.php'; // Ensure this file has the correct database connection

// Check if the logged-in user is a seller or a regular user
if (isset($_SESSION['seller_id'])) {
    // The logged-in user is a seller
    $user_type = 'seller';
    $user_id = $_SESSION['seller_id'];
    $select_user = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
} elseif (isset($_SESSION['user_id'])) {
    // The logged-in user is a regular user
    $user_type = 'user';
    $user_id = $_SESSION['user_id'];
    $select_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
} else {
    // Redirect to the login page if no session is found
    header('Location: ../components/login.php');
    exit();
}

// Fetch the user details from the database
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

// Update profile logic (for both users and sellers)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? $user['name']);
    $phone = htmlspecialchars($_POST['phone'] ?? $user['phone']);
    $address = htmlspecialchars($_POST['address'] ?? $user['address']);

    // Handle profile image upload if necessary
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $valid_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_type, $valid_types)) {
            $image_path = '../uploaded_files/' . $image_name;
            move_uploaded_file($image_tmp, $image_path);

            // Update the image in the database
            if ($user_type == 'seller') {
                $update_image = $conn->prepare("UPDATE sellers SET image = ? WHERE id = ?");
            } else {
                $update_image = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
            }
            $update_image->execute([$image_name, $user_id]);
        } else {
            echo "<script>alert('Invalid image type!');</script>";
        }
    }

    // Update the user's details (name, phone, address)
    if ($user_type == 'seller') {
        $update_user = $conn->prepare("UPDATE sellers SET name = ?, phone = ?, address = ? WHERE id = ?");
    } else {
        $update_user = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    }
    $update_user->execute([$name, $phone, $address, $user_id]);

    // Redirect to the profile page after updating
    header('Location: profile.php?msg=Profile updated successfully');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= ucfirst($user_type) ?> Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* Similar styling as previous profile pages */
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
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .profile-header h1 {
            color: #e63946;
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        .profile-header p {
            font-size: 0.85rem;
            color: #888;
        }

        .profile-info {
            width: 100%;
            text-align: center;
        }

        .profile-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e63946;
            margin-bottom: 12px;
        }

        .profile-info form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .profile-info input[type="text"],
        .profile-info input[type="email"],
        .profile-info input[type="file"],
        .profile-info textarea {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 0.85rem;
            width: 100%;
            max-width: 350px;
            margin-bottom: 6px;
            transition: border-color 0.3s ease;
        }

        .profile-info input[type="text"]:focus,
        .profile-info input[type="email"]:focus,
        .profile-info textarea:focus {
            border-color: #e63946;
        }

        .profile-info label {
            font-size: 0.85rem;
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
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            width: 100%;
            max-width: 350px;
        }

        .profile-btn:hover {
            background-color: #d62828;
        }

        .profile-action-links {
            display: flex;
            justify-content: center;
            margin-top: 14px;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 14px;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            width: 100px;
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
        <?php if (isset($_GET['msg'])): ?>
            <div class="profile-alert"><?= htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="profile-info">
            <img src="../uploaded_files/<?= $user['image'] ?: 'default_image.jpg'; ?>" alt="Profile Image">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" placeholder="Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" disabled>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Phone" required>

                <label for="address">Address</label>
                <textarea id="address" name="address" rows="2" placeholder="Address" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>

                <label for="image">Profile Image</label>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">

                <button type="submit" class="profile-btn">Update Profile</button>
            </form>
        </div>

        <div class="profile-action-links">
            <a href="<?= $user_type == 'seller' ? '../admin_pannel/dashboard.php' : '../user_pannel/product_list.php' ?>" class="action-btn">Back to Home</a>
            <a href="../components/logout.php" class="action-btn">Logout</a>
        </div>

    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
