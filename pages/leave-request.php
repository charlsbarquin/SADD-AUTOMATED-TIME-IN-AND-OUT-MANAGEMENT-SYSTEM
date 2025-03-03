<?php include '../config/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Leave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">📅 Request Leave</h2>
    <form action="../api/submit-leave.php" method="POST">
        <div class="mb-3">
            <label for="professor" class="form-label">Select Professor:</label>
            <select class="form-control" name="professor_id" required>
                <option value="">-- Select Professor --</option>
                <?php
                $result = $conn->query("SELECT id, name FROM professors ORDER BY name ASC");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="leave_date" class="form-label">Leave Date:</label>
            <input type="date" class="form-control" name="leave_date" required>
        </div>
        <div class="mb-3">
            <label for="reason" class="form-label">Reason:</label>
            <textarea class="form-control" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
    </form>
</div>
</body>
</html>
