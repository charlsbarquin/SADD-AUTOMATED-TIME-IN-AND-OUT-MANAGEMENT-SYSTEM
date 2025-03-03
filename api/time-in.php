<?php
include '../config/database.php';

$professor_id = $_POST['professor_id'];
$check_in_time = date("Y-m-d H:i:s");

// Insert the check-in record
$query = "INSERT INTO attendance (professor_id, check_in) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $professor_id, $check_in_time);
$stmt->execute();

// 🔔 Insert a Notification
$notif_message = "Professor ID $professor_id just checked in.";
$notif_type = "check-in";
$is_read = 0;

$query_notif = "INSERT INTO notifications (message, type, created_at, is_read) VALUES (?, ?, NOW(), ?)";
$stmt_notif = $conn->prepare($query_notif);
$stmt_notif->bind_param("ssi", $notif_message, $notif_type, $is_read);
$stmt_notif->execute();

// ✅ Return a response for real-time updates
echo json_encode(["success" => true, "message" => "Check-in recorded successfully"]);
?>
