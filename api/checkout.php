<?php
include '../config/database.php';
header('Content-Type: application/json');

// Ensure professor_id is provided via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['professor_id'])) {
    $professor_id = $_POST['professor_id'];

    // Get the most recent check-in record for the professor
    $query = "SELECT id, check_in, status 
              FROM attendance 
              WHERE professor_id = ? AND check_out IS NULL AND DATE(check_in) = CURDATE() 
              ORDER BY check_in DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $attendance_id = $row['id'];
        $check_in = strtotime($row['check_in']); // Convert check-in time to timestamp

        // Get current time for check-out
        $check_out = date("Y-m-d H:i:s");
        $check_out_time = strtotime($check_out);

        // Calculate work duration
        $work_duration = gmdate("H:i:s", $check_out_time - $check_in);

        // Default status to "Present" after check-out (if no leave status)
        $status = 'Present'; // Mark as Present by default

        // Check if the professor was on leave during check-in (leave status check)
        $leave_query = "SELECT status FROM leave_requests WHERE professor_id = ? AND leave_date = CURDATE() LIMIT 1";
        $leave_stmt = $conn->prepare($leave_query);
        $leave_stmt->bind_param("i", $professor_id);
        $leave_stmt->execute();
        $leave_result = $leave_stmt->get_result();

        if ($leave_result->num_rows > 0) {
            $leave_row = $leave_result->fetch_assoc();
            if ($leave_row['status'] === "Approved") {
                $status = "On Leave"; // If on leave, change status to "On Leave"
            }
        }

        // Update the attendance record with the check-out time, work duration, and status
        $update = "UPDATE attendance SET check_out = ?, work_duration = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("sssi", $check_out, $work_duration, $status, $attendance_id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "✅ Time Out recorded successfully!",
                "work_duration" => $work_duration,
                "status" => $status, // Return updated status (Present or On Leave)
                "check_out" => $check_out
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "❌ Database error: " . $stmt->error
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "❌ No check-in record found for today!"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "❌ Invalid request! Professor ID is missing."
    ]);
}
?>
