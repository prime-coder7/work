<?php
// Include the database connection
include '../components/connected.php'; 

// Check if the user is logged in by verifying session data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Retrieve user_id from session

    // Fetch user profile details from the database
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC); 
    }
} else { 
    // Redirect to login page if not logged in
    header("Location: ../components/login.php"); 
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>F1 Merchandise</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <style>
        /* Header and Sidebar CSS */
        header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff5733;
            text-transform: uppercase;
        }

        #sidebar {
            width: 250px;
            background: #212529;
            color: white;
            position: fixed;
            top: 60px;
            left: -250px;
            bottom: 0;
            transition: all 0.3s ease-in-out;
            z-index: 999;
        }

        #sidebar.active {
            left: 0;
        }

        #sidebar h2 {
            text-align: start;
            padding: 20px 20px 20px 40px;
            background-color: #343a40;
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        #sidebar .nav-link {
            color: white;
            padding: 15px;
            display: block;
        }

        #sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        .main-content {
            margin-left: 250px;
            padding-top: 80px;
            padding-bottom: 60px;
        }

        footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .btn-block {
            margin-top: 10px;
        }
        
        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            #sidebar {
                width: 100%;
                top: 50px;
            }

            #sidebar.active {
                left: 0;
            }

            header {
                flex-direction: column;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <header>
        <button id="toggle-btn" class="btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo">F1</div>
        <div class="user-options">
            <button class="btn" id="notifications-btn">
                <i class="fas fa-bell"></i>
            </button>
            <a href="../index.php" class="btn">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>

    <!-- Sidebar -->
    <nav id="sidebar" class="d-flex flex-column">
        <h2 class="text-light border-bottom">F1</h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../user_pannel/user_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user_pannel/shop.php">
                    <i class="fas fa-store"></i> Shop
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user_pannel/cart.php">
                    <i class="fas fa-shopping-cart"></i> My Cart
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user_pannel/order_history.php">
                    <i class="fas fa-box"></i> My Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user_pannel/user_message.php">
                    <i class="fas fa-comment-dots"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../components/profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../components/logout.php" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Dynamic Content goes here -->
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 F1. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document
            .getElementById("toggle-btn")
            .addEventListener("click", function () {
                document.getElementById("sidebar").classList.toggle("active");
                document.querySelector(".main-content").classList.toggle("active");
            });
    </script>
</body>
</html>
