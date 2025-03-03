<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $professor_id = $_POST['professor_id'] ?? NULL;
    $image_data = $_POST['image_data'] ?? NULL;
    $latitude = $_POST['latitude'] ?? NULL;
    $longitude = $_POST['longitude'] ?? NULL;
    $accuracy = $_POST['accuracy'] ?? NULL;

    // If professor ID is missing, stop execution
    if (!$professor_id) {
        echo json_encode(["status" => "error", "message" => "❌ Professor ID is missing!"]);
        exit;
    }

    // Check if professor exists
    $check_professor = $conn->query("SELECT * FROM professors WHERE id = '$professor_id'");
    if ($check_professor->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "❌ Professor not found!"]);
        exit;
    }

    // Check if the professor is on leave
    $is_on_leave = false; // Assume no leave by default
    $leave_check = $conn->query("SELECT * FROM leave_requests WHERE professor_id = '$professor_id' AND status = 'Approved' AND CURDATE() BETWEEN leave_start AND leave_end");
    if ($leave_check->num_rows > 0) {
        $is_on_leave = true; // If there's an approved leave request, set status to 'On Leave'
    }

    // If the professor is on leave, mark them as "On Leave"
    if ($is_on_leave) {
        $status = 'On Leave';
    } else {
        // Check if already checked in today
        $check_attendance = $conn->query("SELECT * FROM attendance WHERE professor_id = '$professor_id' AND DATE(check_in) = CURDATE()");
        if ($check_attendance->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "⚠️ You have already checked in today."]);
            exit;
        }

        // Determine if the professor is Present or Absent based on check-in time
        $scheduled_time = "08:00:00"; // Assuming the scheduled time is 8 AM. Modify based on your system.
        $late_time = strtotime($scheduled_time); 
        $current_time = strtotime(date("H:i:s"));
        
        // Grace period of 5 minutes
        $grace_period = 5 * 60; // 5 minutes in seconds
        if ($current_time <= ($late_time + $grace_period)) {
            $status = 'Present'; // They are present if they check in within the grace period
        } else {
            $status = 'Absent'; // Otherwise, mark them as Absent
        }
    }

    // Store the image (if received)
    if (!empty($image_data)) {
        $image_name = "checkin_" . time() . ".jpg";
        $image_path = "../uploads/" . $image_name;
        file_put_contents($image_path, file_get_contents($image_data));
    } else {
        $image_name = NULL;
    }

    // Insert Data into Database
    $sql = "INSERT INTO attendance (professor_id, check_in, status, face_scan_image, latitude, longitude, accuracy) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "❌ SQL Prepare Failed: " . $conn->error]));
    }

    $stmt->bind_param("isssdds", $professor_id, $status, $image_name, $latitude, $longitude, $accuracy);
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(["status" => "success", "message" => "✅ Check-in recorded successfully!", "status" => $status]);
    } else {
        die(json_encode(["status" => "error", "message" => "❌ SQL Execution Failed: " . $stmt->error]));
    }
}
?>
