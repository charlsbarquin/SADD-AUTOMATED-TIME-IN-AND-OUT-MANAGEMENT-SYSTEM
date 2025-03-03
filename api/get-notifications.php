<?php
include '../config/database.php';

$query = "SELECT id, message, created_at AS time, type, is_read FROM notifications ORDER BY created_at DESC LIMIT 25";
$result = $conn->query($query);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($notifications);
?>
