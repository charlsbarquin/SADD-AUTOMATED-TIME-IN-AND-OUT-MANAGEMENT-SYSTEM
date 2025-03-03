<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $professor_id = $_POST['professor_id'];
    $leave_date = $_POST['leave_date'];
    $reason = $_POST['reason'];

    // Check if the professor already applied for leave on this date
    $check_query = "SELECT id FROM leave_requests WHERE professor_id = '$professor_id' AND leave_date = '$leave_date'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('❌ You have already requested leave for this date!'); window.history.back();</script>";
        exit;
    }

    // Insert leave request
    $sql = "INSERT INTO leave_requests (professor_id, leave_date, reason) 
            VALUES ('$professor_id', '$leave_date', '$reason')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ Leave request submitted successfully!'); window.location.href='../pages/leave-request.php';</script>";
    } else {
        echo "<script>alert('❌ Database error: " . $conn->error . "'); window.history.back();</script>";
    }
}
?>
