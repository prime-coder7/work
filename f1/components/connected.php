<?php
// Database connection details
$db_name = 'mysql:host=localhost;dbname=f1_db';
$user_name = 'root';
$user_password = '';

$conn = new PDO($db_name, $user_name, $user_password);

if ($conn) {
    echo "connected";
}

// Check if the function is already defined
if (!function_exists('unique_id')) {
    function unique_id() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charsLength = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $randomString.= $chars[mt_rand(0, $charsLength - 1)];
        }
        return $randomString;
    }
}

?>
