<?php
include '../config/database.php';

// Fetch unread notifications, ordered by most recent
$query = "SELECT id, message, type, created_at FROM notifications WHERE is_read = 0 ORDER BY created_at DESC";
$result = $conn->query($query);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Return notifications as JSON
echo json_encode($notifications);
?>
