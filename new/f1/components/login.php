<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../components/connected.php'; // Include database connection

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect user based on role (user or seller)
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_user->execute([$_SESSION['user_id']]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
    
    if ($user['role'] == 'user') {
        header('Location: ../user_pannel/product_list.php');
    } else {
        header('Location: ../admin_pannel/dashboard.php');
    }
    exit();
}

if (isset($_SESSION['seller_id'])) {
    // Redirect seller based on role
    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
    $select_seller->execute([$_SESSION['seller_id']]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    if ($seller['role'] == 'seller') {
        header('Location: ../admin_pannel/dashboard.php');
    } else {
        header('Location: ../user_pannel/product_list.php');
    }
    exit();
}

$warning_msg = []; // Initialize warning messages array

if (isset($_POST['submit'])) {
    // Sanitize inputs
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

    // Check if the email exists in the users table (for normal users)
    $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $select_user->execute([$email]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);

    // Check if the email exists in the sellers table (for sellers)
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ?");
    $select_seller->execute([$email]);
    $seller = $select_seller->fetch(PDO::FETCH_ASSOC);

    // User login logic
    if ($user) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/");

            // Redirect based on role
            header('Location: ../user_pannel/product_list.php');
            exit();
        } else {
            $warning_msg[] = 'Incorrect password.';
        }
    }
    // Seller login logic
    else if ($seller) {
        if (password_verify($pass, $seller['password'])) {
            $_SESSION['seller_id'] = $seller['id'];
            setcookie('seller_id', $seller['id'], time() + (86400 * 30), "/");

            // Redirect based on role
            if ($seller['role'] == 'seller') {
                header('Location: ../admin_pannel/dashboard.php');
            } else {
                header('Location: ../user_pannel/product_list.php');
            }
            exit();
        } else {
            $warning_msg[] = 'Incorrect password.';
        }
    } else {
        $warning_msg[] = 'Invalid email address.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 - Login</title>
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
                    <input type="email" name="email" placeholder="Enter your email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" maxlength="50" required class="box">
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
