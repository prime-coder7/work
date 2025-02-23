<?php
include '../components/connected.php'; // Include database connection

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
    header("Location: login.php");
    exit();
}
?>

<style>
    .logo-img {
        border-radius: 50%; /* Make the profile image round */
        object-fit: cover;
    }
    .profile-detail img,
    .sidebar .profile img {
        border-radius: 50%; /* Round the profile images in both header and sidebar */
    }
    /* Red Theme */
body {
    background-color: #f8f8f8; /* Light background for contrast */
    font-family: Arial, sans-serif;
}

/* Header */
header {
    background-color: #e74c3c; /* Red color for the header */
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo img {
    width: 200px;
}

header .right {
    display: flex;
    align-items: center;
}

header .right .bx {
    font-size: 24px;
    color: #fff;
    margin-right: 20px;
}

header .profile-detail {
    display: flex;
    align-items: center;
}

header .profile-detail .profile {
    display: flex;
    flex-direction: column;
    align-items: center;
}

header .profile-detail .profile img {
    border-radius: 50%;
    width: 150px;
    height: 150px;
}

header .profile-detail .profile p {
    color: #fff;
    font-size: 18px;
    margin-top: 10px;
}

header .profile-detail .profile .flex-btn {
    margin-top: 15px;
}

header .profile-detail .profile .flex-btn .btn {
    background-color: #fff;
    color: #e74c3c;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin: 5px;
}

header .profile-detail .profile .flex-btn .btn:hover {
    background-color: #c0392b;
    color: #fff;
}

/* Sidebar */
.sidebar-container {
    display: flex;
}

.sidebar {
    background-color: #e74c3c; /* Sidebar background color */
    width: 250px;
    padding: 20px;
    height: 100vh;
    color: #fff;
}

.sidebar .profile img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
}

.sidebar .profile p {
    color: #fff;
    font-size: 18px;
    text-align: center;
}

.sidebar h5 {
    color: #fff;
    font-size: 18px;
    margin-bottom: 15px;
}

.sidebar .navbar ul {
    list-style: none;
    padding: 0;
}

.sidebar .navbar ul li {
    margin: 10px 0;
}

.sidebar .navbar ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.sidebar .navbar ul li a:hover {
    background-color: #c0392b;
    padding: 5px;
    border-radius: 5px;
}

.sidebar .social-links {
    margin-top: 30px;
}

.sidebar .social-links a {
    margin: 0 10px;
}

.sidebar .social-links a img {
    width: 30px;
    height: 30px;
}

/* Logo styling */
.logo-img {
    border-radius: 50%;
    object-fit: cover;
}

/* Profile images */
.profile-detail img,
.sidebar .profile img {
    border-radius: 50%; /* Round the profile images */
}

.flex-btn .btn {
    background-color: #e74c3c;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s;
}

.flex-btn .btn:hover {
    background-color: #c0392b;
}

/* For smaller screens (mobile responsiveness) */
@media (max-width: 768px) {
    .sidebar-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        padding: 10px;
    }
}

</style>

<header>
    <div class="logo">
        <img src="https://logos-world.net/wp-content/uploads/2023/12/Formula-One-Logo-1994-500x281.png" width="200">
    </div>
    <div class="right">
        <div class="bx bxs-user" id="user-btn"></div>
        <div class="toggle-btn"><i class="bx bx-menu"></i></div>
    </div>
    <div class="profile-detail">
        <?php if (isset($fetch_profile)): ?>
        <div class="profile">
            <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" class="logo-img" width="150" alt="Profile Image">
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <div class="flex-btn">
                <a href="profile.php" class="btn">Profile</a>
                <a href="../components/logout.php" onclick="return confirm('Logout from this website?');" class="btn">Logout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</header>

<div class="sidebar-container">
    <div class="sidebar">
        <?php if (isset($fetch_profile)): ?>
        <div class="profile">
            <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" class="logo-img" width="100" alt="Profile Image">
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
        </div>
        <?php endif; ?>
        
        <h5>Menu</h5>
        <div class="navbar">
            <ul>
                <li><a href="../user_pannel/dashboard.php"><i class="bx bxs-home-smile"></i>Dashboard</a></li>
                <li><a href="../user_pannel/product_list.php"><i class="bx bxs-shopping-bag"></i>Product List</a></li>
                <li><a href="../user_pannel/order_history.php"><i class="bx bxs-history"></i>Order History</a></li>
                <li><a href="profile.php"><i class="bx bxs-user-detail"></i>Account</a></li>
                <li><a href="../components/logout.php" onclick="return confirm('Logout from this website?');"><i class="bx bxs-log-out"></i>Logout</a></li>
            </ul>
        </div><br><br><br><center>
    <div class="social-links">
        <a href="https://facebook.com" target="_blank"><img src="https://freepnglogo.com/images/all_img/1713419302Black_Facebook_PNG.png" alt="Facebook" width="25"></a>
        <a href="https://instagram.com" target="_blank"><img src="https://freepnglogo.com/images/all_img/1715966613instagram-logo-black-and-white-png.png" alt="Instagram" width="25"></a>
        <a href="https://linkedin.com" target="_blank"><img src="https://tse1.mm.bing.net/th?id=OIP.MaqcSfiMThZuoDpaYYsSMgHaHa&pid=Api&P=0&h=180" alt="LinkedIn" width="25"></a>
        <a href="https://twitter.com" target="_blank"><img src="https://freepnglogo.com/images/all_img/1707226109new-twitter-logo-png.png" alt="Twitter" width="25"></a>
        <a href="https://pinterest.com" target="_blank"><img src="https://pnggrid.com/wp-content/uploads/2024/11/Black-Pinterest-Logo-1024x1024.png" alt="Pinterest" width="25"></a>
    </div>
    </center>
    </div>
</div>
