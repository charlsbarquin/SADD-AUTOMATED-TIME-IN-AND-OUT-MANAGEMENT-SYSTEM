<?php
include '../config/database.php';

// Set the cutoff time for auto-timeout (e.g., 6:00 PM)
$cutoff_time = "18:00:00";

// Get professors who have checked in but not checked out
$sql = "SELECT id, professor_id FROM attendance 
        WHERE check_out IS NULL 
        AND check_in < CURDATE() + INTERVAL '$cutoff_time' HOUR_SECOND";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $attendance_id = $row['id'];

    // Auto-timeout the professor
    $update_sql = "UPDATE attendance 
                   SET check_out = NOW(), auto_timeout = 1 
                   WHERE id = '$attendance_id'";

    $conn->query($update_sql);
}

echo json_encode(["status" => "success", "message" => "Auto-timeout process completed."]);
?>
