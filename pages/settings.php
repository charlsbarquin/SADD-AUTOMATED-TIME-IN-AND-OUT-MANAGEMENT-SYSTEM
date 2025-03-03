<?php
include '../config/database.php';
session_start();

// Ensure 'user_role' is set to prevent undefined key errors
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'user'; // Default role if not logged in
}

// Fetch current settings
$sql = "SELECT * FROM settings WHERE id = 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();

// Handle settings update (Accessible to all users)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
    $late_cutoff = trim($_POST['late_cutoff']);
    $timezone = trim($_POST['timezone']);
    $allow_auto_timeout = isset($_POST['allow_auto_timeout']) ? 1 : 0;

    $update_sql = "UPDATE settings SET late_cutoff = ?, timezone = ?, allow_auto_timeout = ? WHERE id = 1";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $late_cutoff, $timezone, $allow_auto_timeout);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Settings updated successfully!";
        header("Location: settings.php");
        exit;
    } else {
        echo "Error updating settings.";
    }
}

// Handle Reset Attendance Data (Only for Admins)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_attendance']) && $_SESSION['user_role'] === 'admin') {
    $conn->query("DELETE FROM attendance");
    $_SESSION['success_message'] = "Attendance data reset successfully!";
    header("Location: settings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Attendance System</title>

    <!-- Bootstrap & Custom Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="container mt-4">
        <h2 class="text-center">⚙️ System Settings</h2>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- General Settings (Accessible to All Users) -->
        <div class="card p-4">
            <h4><i class="fas fa-clock"></i> General Settings</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Late Cutoff Time</label>
                    <input type="time" name="late_cutoff" class="form-control" value="<?= $settings['late_cutoff']; ?>" required 
                           oninput="updateLateCutoffPreview()">
                    <small class="text-muted">Late if checked in after: <strong id="lateCutoffPreview"><?= $settings['late_cutoff']; ?></strong></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-control">
                        <option value="Asia/Manila" <?= ($settings['timezone'] == "Asia/Manila") ? 'selected' : ''; ?>>Asia/Manila</option>
                        <option value="UTC" <?= ($settings['timezone'] == "UTC") ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?= ($settings['timezone'] == "America/New_York") ? 'selected' : ''; ?>>America/New York</option>
                        <option value="Europe/London" <?= ($settings['timezone'] == "Europe/London") ? 'selected' : ''; ?>>Europe/London</option>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="allow_auto_timeout" class="form-check-input" <?= $settings['allow_auto_timeout'] ? 'checked' : ''; ?>>
                    <label class="form-check-label">Enable Auto Logout for Missing Time-Out</label>
                </div>
                <button type="submit" name="save_settings" class="btn btn-primary w-100">Save Changes</button>
            </form>
        </div>

        <!-- Dangerous Actions (Only for Admins) -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') : ?>
        <div class="card p-4 mt-4">
            <h4 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Admin Actions</h4>
            <p>These actions are restricted to administrators. Resetting attendance data cannot be undone.</p>
            <form method="POST" onsubmit="return confirmReset();">
                <button type="submit" name="reset_attendance" class="btn btn-danger w-100">Reset Attendance Data</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
        }

        function updateLateCutoffPreview() {
            document.getElementById("lateCutoffPreview").innerText = document.querySelector("input[name='late_cutoff']").value;
        }

        function confirmReset() {
            return confirm("⚠️ WARNING: This will permanently delete all attendance records. Are you sure?");
        }

        $(document).ready(function() {
            if (localStorage.getItem("dark-mode") === "enabled") {
                document.body.classList.add("dark-mode");
            }
        });
    </script>

</body>

</html>
