<?php 
include '../components/connected.php';

if (isset($_POST['submit'])) {

    // Generate a unique ID for the seller
    $id = unique_id();

    // Sanitize and validate the input fields
    $name = $_POST['name'] ?? '';
    $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Sanitize name

    $email = $_POST['email'] ?? '';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Sanitize email

    $pass = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    // Hash the password and confirm password using password_hash() for better security
    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
    $cpass_hashed = password_hash($cpass, PASSWORD_DEFAULT);

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);  // Clean the image name
        $ext = pathinfo($image, PATHINFO_EXTENSION); // Get the file extension
        $rename = unique_id() . '.' . $ext;  // Generate a unique file name
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;

        // Move the uploaded image to the target folder
        move_uploaded_file($image_tmp_name, $image_folder);
    } else {
        // No image uploaded, set $image and $rename to null
        $image = null;
        $rename = null;
    }

    // Check if email already exists
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ?");
    $select_seller->execute([$email]);

    if ($select_seller->rowCount() > 0) {
        $warning_msg[] = 'Email already exists!';
    } else {
        // Validate password matching
        if ($pass !== $cpass) {
            $warning_msg[] = 'Confirm password does not match!';
        } else {
            // Insert the seller data into the database with password_hash()
            $insert_seller = $conn->prepare("INSERT INTO sellers (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
            $insert_seller->execute([$id, $name, $email, $pass_hashed, $rename]);  // Use $pass_hashed for password and $rename for image

            $success_msg[] = 'New seller registered! Please log in now.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 - Seller Registration Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Register Now</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Your Name <span>*</span></p>
                        <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Your Email <span>*</span></p>
                        <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>Your Password <span>*</span></p>
                        <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Confirm Password <span>*</span></p>
                        <input type="password" name="cpass" placeholder="Confirm your password" maxlength="50" required class="box">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>Your Profile <span>*</span></p>
                <input type="file" name="image" accept="image/*" class="box">
            </div>
            <p class="link">Already have an account? <a href="login.php">Login now</a></p>
            <input type="submit" name="submit" value="Register Now" class="btn">
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>
</html>
