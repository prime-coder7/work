<?php
include '../components/connected.php'; // Connected.php includes session_start()

// Check if the seller is logged in by verifying session data
if (isset($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id']; // Retrieve seller_id from session
} 
// else {
//     // Redirect to login page if not logged in
//     header("Location: login.php");
//     exit();
// }
?>

<header>
    <div class="logo">
        <img src="https://logos-world.net/wp-content/uploads/2023/12/Formula-One-Logo-1994-500x281.png" width="200">
    </div>
    <div class="right">
        <div class="bx bxs-user" id="user-btn"></div>
        <div class="toggle-btn"><i class="bx bx-menu"></i></div>
    </div>
    <div class="profile-detail">
        <?php
        // Fetch seller profile details from the database
        $select_profile = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
        $select_profile->execute([$seller_id]);

        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="profile">
            <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" class="logo-img" width="150">
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <div class="flex-btn">
                <a href="profile.php" class="btn">Profile</a>
                <a href="../components/logout.php" onclick="return confirm('Logout from this website?');" class="btn">Logout</a>
            </div>
        </div>
        <?php } ?>
    </div>
</header>

<div class="sidebar-container">
    <div class="sidebar">
        <?php
        // Fetch the seller profile details for the sidebar
        $select_profile = $conn->prepare("SELECT * FROM `sellers` WHERE id = ?");
        $select_profile->execute([$seller_id]);

        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="profile">
            <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" class="logo-img" width="100">
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
        </div>
        <?php } ?>
        <h5>Menu</h5>
        <div class="navbar">
            <ul>
                <li><a href="../admin_pannel/dashboard.php"><i class="bx bxs-home-smile"></i>Dashboard</a></li>
                <li><a href="../admin_pannel/add_product.php"><i class="bx bxs-shopping-bags"></i>Add Product</a></li>
                <li><a href="../admin_pannel/view_product.php"><i class="bx bxs-food-menu"></i>View Product</a></li>
                <li><a href="../components/profile.php"><i class="bx bxs-user-detail"></i>Account</a></li>
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
