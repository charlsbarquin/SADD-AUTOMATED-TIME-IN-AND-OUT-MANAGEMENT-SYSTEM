<?php
include '../config/database.php';
header('Content-Type: application/json');

// Query to get recent history (Professor name, status, and check-in time)
$query = "SELECT p.name, a.status, a.check_in
          FROM attendance a
          JOIN professors p ON a.professor_id = p.id
          ORDER BY a.check_in DESC LIMIT 5"; // Adjust LIMIT based on your needs

$result = $conn->query($query);

$history = [];
while ($row = $result->fetch_assoc()) {
    // Format the check-in time to 12-hour format (g:i A)
    $formatted_check_in_time = date("g:i A", strtotime($row['check_in'])); // 12-hour format with AM/PM

    // Handle status dynamically if needed
    $status = $row['status'] ? $row['status'] : 'Present'; // Ensure status is set to 'Present' if null

    // Add the formatted history record to the array
    $history[] = [
        'name' => $row['name'],
        'status' => $status, // Professor's status
        'check_in' => $formatted_check_in_time // Formatted 12-hour check-in time
    ];
}

echo json_encode($history); // Ensure you're returning valid JSON
?>
