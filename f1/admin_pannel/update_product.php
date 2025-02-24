<?php
// Start the session to ensure access to the seller's session data
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../components/connected.php'; // Ensure this file has the correct database connection

// Check if the seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit(); // Stop execution if the seller is not logged in
}

// Check if the product ID is provided
if (!isset($_GET['product_id'])) {
    header('Location: dashboard.php');
    exit();
}

$product_id = $_GET['product_id'];
$seller_id = $_SESSION['seller_id'];

// Fetch product details
$select_product = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$select_product->execute([$product_id, $seller_id]);
$product = $select_product->fetch(PDO::FETCH_ASSOC);

// If product not found, redirect to dashboard
if (!$product) {
    header('Location: dashboard.php');
    exit();
}

// Function to handle image upload and product update
function update_product($conn, $product_id, $seller_id)
{
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);

    // Handle the product image upload
    $image = $_FILES['image']['name'];
    if (!empty($image)) {
        $image = uniqid() . '_' . filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $image;

        // Check image size
        $image_size = $_FILES['image']['size'];
        if ($image_size > 2000000) {  // 2MB size limit
            return 'Image size is too large';
        }

        // Move uploaded image to folder
        move_uploaded_file($image_tmp_name, $image_folder);

        // Update product with new image
        $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ? AND seller_id = ?");
        $update_query->execute([$name, $price, $description, $stock, $image, $product_id, $seller_id]);
    } else {
        // If no image uploaded, update without changing the image
        $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE id = ? AND seller_id = ?");
        $update_query->execute([$name, $price, $description, $stock, $product_id, $seller_id]);
    }

    return 'Product updated successfully';
}

// Handle form submission for product update
if (isset($_POST['update'])) {
    $message = update_product($conn, $product_id, $seller_id);
    if ($message === 'Product updated successfully') {
        header('Location: dashboard.php?msg=Product+updated+successfully'); // Redirect after success
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
    <title>Formula 1 - Update Product</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .center-btn {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="main-container">
        <?php include '../components/admin_header.php'; ?>

        <section class="post-editor">
            <div class="heading">
                <h1>Update Product</h1>
                <img src="https://tse4.mm.bing.net/th?id=OIP.zgnLbe1w5yJKtr-_Nbf-hwHaHa&pid=Api&P=0&h=180">
            </div>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <div class="input-field">
                        <p>Product Name<span>*</span></p>
                        <input type="text" name="name" maxlength="100"
                            value="<?= htmlspecialchars($product['name']); ?>" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Product Price<span>*</span></p>
                        <input type="number" name="price" maxlength="100"
                            value="<?= htmlspecialchars($product['price']); ?>" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Product Details<span>*</span></p>
                        <textarea name="description" required maxlength="1000"
                            class="box"><?= htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="input-field">
                        <p>Product Stock<span>*</span></p>
                        <input type="number" name="stock" maxlength="10" min="0" max="999999999"
                            value="<?= htmlspecialchars($product['stock']); ?>" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Product Image<span>*</span></p>
                        <input type="file" name="image" accept="image/*" class="box">
                        <img src="../uploaded_files/<?= htmlspecialchars($product['image']); ?>"
                            alt="Current Product Image" width="100">
                    </div>
                    <div class="flex-btn">
                        <input type="submit" name="update" value="Update Product" class="btn">
                    </div>
                    <p class="error-msg"><?= isset($warning_msg) ? $warning_msg : ''; ?></p>
                </form>

            </div>
            <!-- Centered button to go back to view product page -->
            <div class="center-btn">
                <a href="view_product.php?product_id=<?= urlencode($product['id']); ?>" class="btn">Back to View Product</a>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/admin_script.js"></script>
</body>

</html>