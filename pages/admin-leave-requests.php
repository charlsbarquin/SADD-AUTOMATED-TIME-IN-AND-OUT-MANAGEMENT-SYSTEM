<?php include '../config/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">📝 Manage Leave Requests</h2>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Leave Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT lr.id, p.name, lr.leave_date, lr.reason, lr.status 
                                    FROM leave_requests lr 
                                    JOIN professors p ON lr.professor_id = p.id 
                                    ORDER BY lr.leave_date ASC");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['leave_date']}</td>
                        <td>{$row['reason']}</td>
                        <td><span class='badge bg-" . 
                            ($row['status'] == 'Approved' ? "success" : ($row['status'] == 'Rejected' ? "danger" : "warning")) . 
                            "'>{$row['status']}</span></td>
                        <td>
                            <a href='../api/manage-leave.php?id={$row['id']}&action=approve' class='btn btn-success btn-sm'>Approve</a>
                            <a href='../api/manage-leave.php?id={$row['id']}&action=reject' class='btn btn-danger btn-sm'>Reject</a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
