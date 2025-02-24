<?php
ob_start();
session_start();

// Include database connection
include('../components/connected.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Include header with sidebar
include('../components/user_base.php');
?>

<!-- User Dashboard Content -->
<div class="container mt-5">
  <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
  <div class="row">
    <div class="col-md-6">
      <h4>Profile Details</h4>
      <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
      <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>
    <div class="col-md-6">
      <h4>Recent Activity</h4>
      <!-- Add any recent activity or shopping history here -->
    </div>
  </div>
</div>

<?php ob_end_flush(); ?>

<style>
/* Dashboard specific CSS */
.container {
    margin-left: 270px; /* Adjust to prevent overlap with sidebar */
    padding-top: 30px;
}

.row {
    margin-top: 20px;
}

h4 {
    font-size: 1.5rem;
    color: #343a40;
}

h2 {
    font-size: 2rem;
    font-weight: bold;
    color: #343a40;
}

p {
    font-size: 1rem;
    color: #555;
}
</style>
