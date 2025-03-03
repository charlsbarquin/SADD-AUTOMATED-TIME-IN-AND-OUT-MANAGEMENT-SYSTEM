<?php
include '../config/database.php';

$professor_id = $_POST['professor_id'];
$check_out_time = date("Y-m-d H:i:s");

// Update the attendance record with check-out time
$query = "UPDATE attendance SET check_out = ? WHERE professor_id = ? AND check_out IS NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $check_out_time, $professor_id);
$stmt->execute();

// 🔔 Insert a Notification
$notif_message = "Professor ID $professor_id just checked out.";
$notif_type = "check-out";
$is_read = 0;

$query_notif = "INSERT INTO notifications (message, type, created_at, is_read) VALUES (?, ?, NOW(), ?)";
$stmt_notif = $conn->prepare($query_notif);
$stmt_notif->bind_param("ssi", $notif_message, $notif_type, $is_read);
$stmt_notif->execute();

// ✅ Return a response for real-time updates
echo json_encode(["success" => true, "message" => "Check-out recorded successfully"]);
?>
