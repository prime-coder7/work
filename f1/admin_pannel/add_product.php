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

// Function to generate a unique product ID
function unique_id() {
    return uniqid('prod_', true); // Generates a unique product ID
}

// Function to insert the product (for both published and drafts)
function add_product($status, $conn, $seller_id) {
    $id = unique_id();

    // Use FILTER_SANITIZE_FULL_SPECIAL_CHARS instead of FILTER_SANITIZE_STRING to prevent deprecation warnings
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT); // Ensure stock is an integer

    // Handle the product image upload
    $image = $_FILES['image']['name'];
    $image = uniqid() . '_' . filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Add unique prefix
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/' . $image;

    // Check if the image already exists for the seller
    $select_image = $conn->prepare("SELECT * FROM products WHERE image = ? AND seller_id = ?");
    $select_image->execute([$image, $seller_id]);

    if (isset($image)) {
        if ($select_image->rowCount() > 0) {
            return 'Image name repeated';
        } elseif ($image_size > 2000000) {  // Check image size limit (2MB)
            return 'Image size is too large';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);  // Move uploaded image to folder
        }
    } else {
        $image = '';  // Set empty image if not uploaded
    }

    // Insert the product into the database if no duplicate image found
    if ($select_image->rowCount() === 0) {
        // Check the actual column names in the products table and use the correct name (products_details or another)
        $insert_product = $conn->prepare("INSERT INTO products (id, seller_id, name, price, image, stock, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_product->execute([$id, $seller_id, $name, $price, $image, $stock, $description, $status]);
        return 'Product inserted successfully';
    } else {
        return 'Product upload failed (image name conflict)';
    }
}

// Handle form submissions for both "Publish" and "Draft"
if (isset($_POST['publish'])) {
    $message = add_product('active', $conn, $_SESSION['seller_id']);
    if ($message === 'Product inserted successfully') {
        header('Location: dashboard.php?msg=Product+added+successfully'); // Redirect after success
        exit();
    } else {
        $warning_msg = $message; // Store the failure message
    }
}

if (isset($_POST['draft'])) {
    $message = add_product('inactive', $conn, $_SESSION['seller_id']);
    if ($message === 'Product inserted successfully') {
        header('Location: dashboard.php?msg=Product+saved+as+draft'); // Redirect after success
        exit();
    } else {
        $warning_msg = $message; // Store the failure message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formula 1 - Seller Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<div class="main-container">
    <?php include '../components/admin_header.php'; ?>
    
    <section class="post-editor">
        <div class="heading">
            <h1>Add Product</h1>
            <img src="https://tse4.mm.bing.net/th?id=OIP.zgnLbe1w5yJKtr-_Nbf-hwHaHa&pid=Api&P=0&h=180">
        </div>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <div class="input-field">
                    <p>Product Name<span>*</span></p>
                    <input type="text" name="name" maxlength="100" placeholder="Add product name" required class="box">
                </div>
                <div class="input-field">
                    <p>Product Price<span>*</span></p>
                    <input type="number" name="price" maxlength="100" placeholder="Add product price" required class="box">
                </div>
                <div class="input-field">
                    <p>Product Details<span>*</span></p>
                    <textarea name="description" required maxlength="1000" placeholder="Add product details" class="box"></textarea>
                </div>
                <div class="input-field">
                    <p>Product Stock<span>*</span></p>
                    <input type="number" name="stock" maxlength="10" min="0" max="999999999" placeholder="Add product stock" required class="box">
                </div>
                <div class="input-field">
                    <p>Product Image<span>*</span></p>
                    <input type="file" name="image" accept="image/*" required class="box">
                </div>
                <div class="flex-btn">
                    <input type="submit" name="publish" value="Add Product" class="btn">
                    <input type="submit" name="draft" value="Save as Draft" class="option-btn">
                </div>
                <p class="error-msg"><?= isset($warning_msg) ? $warning_msg : ''; ?></p>
            </form>
        </div>
    </section>
</div>

</body>
</html>
