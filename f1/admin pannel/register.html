<?php
include '../components/connected.php';

if (isset($_POST['submit'])) {

    $id = unique_id();
    $name = $_POST['name'] ?? '';
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'] ?? '';
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    $pass_hashed = sha1($pass);
    $cpass_hashed = sha1($cpass);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;
    } else {
        $image = null;
        $rename = null;
    }

    // Check if email already exists
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ?");
    $select_seller->execute([$email]);

    if ($select_seller->rowCount() > 0) {
        $warning_msg[] = 'Email already exists!';
    } else {
        if ($pass !== $cpass) {
            $warning_msg[] = 'Confirm password does not match!';
        } else {
            if ($rename) {
                move_uploaded_file($image_tmp_name, $image_folder);
            }

            $insert_seller = $conn->prepare("INSERT INTO sellers (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
            $insert_seller->execute([$id, $name, $email, $cpass_hashed, $rename]);

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
                <input type="file" name="image" accept="image/*" required class="box">
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