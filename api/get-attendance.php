<?php
include '../config/database.php';

// Fetch latest attendance records with leave status and calculate work duration
$sql = "SELECT a.*, p.name, 
        (SELECT status FROM leave_requests 
         WHERE professor_id = a.professor_id 
         AND leave_date = CURDATE() 
         LIMIT 1) AS leave_status,
         TIMEDIFF(a.check_out, a.check_in) AS work_duration
        FROM attendance a
        JOIN professors p ON a.professor_id = p.id
        ORDER BY a.check_in DESC 
        LIMIT 10"; // Get latest 10 records

$result = $conn->query($sql);

$attendance = [];
while ($row = $result->fetch_assoc()) {
    // Format the check-in time to 12-hour format with AM/PM
    $formatted_check_in_time = date("g:i A", strtotime($row['check_in'])); // g:i A for 12-hour format with AM/PM
    
    // Default status is 'Present'
    $status = "Present"; 

    // Check if the professor is on leave
    if (!empty($row['leave_status']) && $row['leave_status'] === "Approved") {
        $status = "On Leave"; // If the professor has an approved leave, set status to "On Leave"
    }

    // Prepare the attendance record with the dynamically calculated status
    $attendance[] = [
        "face_scan_image" => !empty($row['face_scan_image']) ? '../uploads/' . $row['face_scan_image'] : null,
        "name" => $row['name'],
        "check_in" => $formatted_check_in_time, // Use the formatted 12-hour time for check_in
        "check_out" => !empty($row['check_out']) ? $row['check_out'] : null, // Return NULL if still not checked out
        "work_duration" => !empty($row['check_out']) ? $row['work_duration'] : null, // Handle if still pending
        "recorded_at" => $row['checkin_date'],
        "latitude" => $row['latitude'],
        "longitude" => $row['longitude'],
        "location_link" => (!empty($row['latitude']) && !empty($row['longitude']))
            ? "https://www.google.com/maps?q={$row['latitude']},{$row['longitude']}"
            : null, // Return NULL if no location data
        "status" => $status // Use dynamically determined status (Present, Absent, On Leave)
    ];
}

// Fetch the count of pending check-outs (professors who have checked in but not checked out today)
$pendingCheckoutQuery = "SELECT COUNT(*) AS pending_checkouts 
                         FROM attendance 
                         WHERE check_out IS NULL AND DATE(check_in) = CURDATE()";
$pendingCheckoutResult = $conn->query($pendingCheckoutQuery);
$pendingCheckoutRow = $pendingCheckoutResult->fetch_assoc();
$pendingCheckouts = $pendingCheckoutRow['pending_checkouts'];

// Return as JSON including the pending check-outs count
header('Content-Type: application/json');
echo json_encode([
    "attendance" => $attendance, // Return the attendance records
    "pending_checkouts" => $pendingCheckouts // Return the count of pending check-outs
], JSON_PRETTY_PRINT);
?>
