<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../components/connected.php'; // Include database connection

// Check if the user is already logged in
if (isset($_SESSION['seller_id'])) {
    header('Location: dashboard.php');
    exit();
}

$warning_msg = []; // Initialize warning messages array

if (isset($_POST['submit'])) {
    // Sanitize inputs
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

    // Check if user exists with the provided email
    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email = ?");
    $select_seller->execute([$email]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    if ($seller) {
        // Use password_verify to check the entered password
        if (password_verify($pass, $seller['password'])) {
            // Successful login
            $_SESSION['seller_id'] = $seller['id'];
            setcookie('seller_id', $seller['id'], time() + (86400 * 30), "/");
            header('Location: dashboard.php');
            exit();
        } else {
            // Invalid password
            $warning_msg[] = 'Invalid email or password.';
        }
    } else {
        // No user found with this email
        $warning_msg[] = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 - Seller Login</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form action="" method="POST" class="register">
            <h3>Login Now</h3>

            <div class="flex">
                <div class="input-field">
                    <p>Your email <span>*</span></p>
                    <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
                </div>
            </div>

            <div class="col">
                <div class="input-field">
                    <p>Your password <span>*</span></p>
                    <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
                </div>
            </div>

            <p class="link">Don't have an account? <a href="register.php">Register now</a></p>

            <input type="submit" name="submit" value="Login Now" class="btn">
        </form>
    </div>

    <?php if (!empty($warning_msg)) { ?>
        <script>
            let messages = <?php echo json_encode($warning_msg); ?>;
            messages.forEach(function(msg) {
                swal("Warning!", msg, "warning");
            });
        </script>
    <?php } ?>
</body>
</html>