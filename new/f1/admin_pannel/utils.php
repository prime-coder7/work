<?php
include __DIR__ . '/../components/connected.php';


$test_query = $conn->query("SELECT * FROM message LIMIT 5");
$test_messages = $test_query->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>"; print_r($test_messages); echo "</pre>";
?>