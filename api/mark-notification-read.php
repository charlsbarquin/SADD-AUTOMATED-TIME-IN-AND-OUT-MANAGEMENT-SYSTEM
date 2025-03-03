<?php
include '../config/database.php';

$notification_id = $_POST['notification_id'];

// Update the notification status to "read"
$query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $notification_id);
$stmt->execute();

echo json_encode(["success" => true]);
?>
