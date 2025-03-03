<?php
include '../config/database.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $sql = "UPDATE leave_requests SET status='Approved' WHERE id='$id'";
    } elseif ($action == 'reject') {
        $sql = "UPDATE leave_requests SET status='Rejected' WHERE id='$id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ Leave request updated!'); window.location.href='../pages/admin-leave-requests.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating request: " . $conn->error . "'); window.history.back();</script>";
    }
}
?>
