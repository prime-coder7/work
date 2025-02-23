<?php
include '../components/connected.php';

if (isset($_POST['submit'])) {

    // Generate a unique ID for the user/seller
    $id = unique_id();

    // Sanitize and validate the input fields
    $name = $_POST['name'] ?? '';
    $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Sanitize name

    $email = $_POST['email'] ?? '';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Sanitize email

    $pass = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    // Password strength validation (optional)
    // if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{4,}$/', $pass)) {
    //     $warning_msg[] = 'Password must be at least 4 characters long and contain at least one letter and one number.';
    // }

    // Hash the password and confirm password using password_hash() for better security
    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
    $cpass_hashed = password_hash($cpass, PASSWORD_DEFAULT);

    // Get the selected role (user or seller)
    $role = $_POST['role'] ?? 'user'; // Default role is 'user'

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);  // Clean the image name
        $ext = pathinfo($image, PATHINFO_EXTENSION); // Get the file extension
        $rename = unique_id() . '.' . $ext;  // Generate a unique file name

        // Image type validation
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed_extensions)) {
            $warning_msg[] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        }

        // Check file size (max 2MB)
        if ($_FILES['image']['size'] > 2000000) {
            $warning_msg[] = 'File size must be under 2MB.';
        }

        // Move the uploaded image to the target folder
        if (empty($warning_msg)) {
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../uploaded_files/' . $rename;
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    } else {
        // No image uploaded, set $image and $rename to null
        $image = null;
        $rename = null;
    }

    // Check if email already exists
    if ($role == 'user') {
        $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $select_user->execute([$email]);

        if ($select_user->rowCount() > 0) {
            $warning_msg[] = 'Email already exists!';
        } else {
            // Validate password matching
            if ($pass !== $cpass) {
                $warning_msg[] = 'Confirm password does not match!';
            } else {
                // Insert the user data into the users table with password_hash() and role
                $insert_user = $conn->prepare("INSERT INTO users (id, name, email, password, image, role) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_user->execute([$id, $name, $email, $pass_hashed, $rename, $role]);

                $success_msg[] = 'New user registered! Please log in now.';
            }
        }
    } else if ($role == 'seller') {
        $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ?");
        $select_seller->execute([$email]);

        if ($select_seller->rowCount() > 0) {
            $warning_msg[] = 'Email already exists!';
        } else {
            // Validate password matching
            if ($pass !== $cpass) {
                $warning_msg[] = 'Confirm password does not match!';
            } else {
                // Insert the seller data into the sellers table with password_hash() and role
                $insert_seller = $conn->prepare("INSERT INTO sellers (id, name, email, password, image, role) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_seller->execute([$id, $name, $email, $pass_hashed, $rename, $role]);

                $success_msg[] = 'New seller registered! Please log in now.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 - User Registration Page</title>
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
                <p>Select Role <span>*</span></p>
                <select name="role" required class="box">
                    <option value="user">User</option>
                    <option value="seller">Seller</option>
                </select>
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
