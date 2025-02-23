<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session
}

// Database connection details for SQLite
$db_file = __DIR__ . '/../f1_db.sqlite3'; // Use relative path to your SQLite file

try {
    // Connect to the SQLite database using PDO
    $conn = new PDO("sqlite:$db_file");

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log the error to a file with timestamp for better tracking
    error_log("[" . date('Y-m-d H:i:s') . "] Connection failed: " . $e->getMessage());

    // Display a user-friendly message to the user
    echo "Sorry, we are experiencing technical issues. Please try again later.";
    exit(); // Exit to prevent further code execution
}

// Function to generate a unique ID securely
if (!function_exists('unique_id')) {
    function unique_id() {
        // Using random_bytes for secure random ID generation
        $bytes = random_bytes(16); // 16 bytes = 128 bits
        return bin2hex($bytes); // Convert bytes to hexadecimal for easy handling
    }
}
?>
