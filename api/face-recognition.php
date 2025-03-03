<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image_data = $_POST['image_data'];

    // Simulating Face Recognition (Replace this with actual face-matching logic)
    $professor = $conn->query("SELECT * FROM professors WHERE profile_image != '' LIMIT 1")->fetch_assoc();

    if ($professor) {
        $professor_id = $professor['id'];
        $name = $professor['name'];

        // Check if professor already checked in today
        $check = $conn->query("SELECT * FROM attendance WHERE professor_id = '$professor_id' AND DATE(check_in) = CURDATE()");
        if ($check->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Already checked in today"]);
            exit;
        }

        // Record Check-In
        $sql = "INSERT INTO attendance (professor_id, check_in, face_scan_image, status) 
                VALUES ('$professor_id', NOW(), '$image_data', 'Present')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Check-in recorded", "name" => $name]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Face not recognized"]);
    }
}
?>
