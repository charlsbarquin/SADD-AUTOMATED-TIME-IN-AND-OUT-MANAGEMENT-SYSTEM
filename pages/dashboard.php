<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Automated Attendance System</title>

    <!-- Bootstrap & Custom Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- External CSS for organization -->

    <!-- jQuery & Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php include '../includes/navbar.php'; ?>

<body>

    <!-- Main Content -->
    <div class="container mt-4">

        <!-- Statistics Section -->
        <div class="row g-3 text-center">
            <div class="col-md-4">
                <div class="card p-3 bg-primary text-white">
                    <h4>Total Professors</h4>
                    <h2 id="total-professors">...</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 bg-success text-white">
                    <h4>Today’s Attendance</h4>
                    <h2 id="today-attendance">...</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 bg-warning text-white">
                    <h4>Late Entries</h4>
                    <h2 id="late-count">...</h2>
                </div>
            </div>
        </div>

        <!-- Attendance Chart -->
        <div class="card mt-4 p-3">
            <h3 class="text-center">📊 Attendance Overview</h3>
            <canvas id="attendanceChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Real-Time Attendance Logs -->
        <div class="card mt-4 p-3">
            <h3 class="text-center">Recent Attendance</h3>
            <table class="table table-bordered text-center">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Name</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-logs">
                    <!-- Attendance logs will be loaded dynamically -->
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function fetchDashboardData() {
            $.ajax({
                url: "../api/dashboard-data.php",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    $("#total-professors").text(data.total_professors);
                    $("#today-attendance").text(data.today_attendance);
                    $("#late-count").text(data.late_entries);

                    // Load Chart Data
                    updateAttendanceChart(data.attendance_chart);
                }
            });
        }

        function fetchAttendanceLogs() {
            $.ajax({
                url: "../api/get-attendance.php", // Ensure this URL is correct
                method: "GET",
                dataType: "json",
                success: function(data) {
                    // Log the response data to the console for debugging
                    console.log(data);

                    // Ensure the "attendance" key exists
                    if (data.attendance && Array.isArray(data.attendance) && data.attendance.length > 0) {
                        let logs = $("#attendance-logs");
                        logs.html(""); // Clear previous data

                        data.attendance.forEach(row => {
                            // Append each row to the table
                            logs.append(`
                                <tr>
                                    <td>${row.name || "N/A"}</td>
                                    <td>${row.check_in || "N/A"}</td>
                                    <td>${row.check_out || "Pending"}</td>
                                    <td>${row.status || "N/A"}</td>
                                </tr>
                            `);
                        });
                    } else {
                        console.log("No data available or unexpected response format.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching attendance logs:", error);
                }
            });
        }

        function updateAttendanceChart(chartData) {
            if (!chartData || chartData.labels.length === 0 || chartData.data.length === 0) {
                console.warn("No data available for the chart.");
                return;
            }

            const ctx = document.getElementById("attendanceChart").getContext("2d");

            // Destroy existing chart instance if it exists
            if (window.attendanceChartInstance) {
                window.attendanceChartInstance.destroy();
            }

            window.attendanceChartInstance = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: "Attendance Count",
                        data: chartData.data,
                        backgroundColor: "rgba(0, 123, 255, 0.7)",
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: Math.max(...chartData.data) + 5 // Add padding to max value
                        }
                    },
                    plugins: {
                        legend: { display: false } // Hide the legend to save space
                    }
                }
            });
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
        }

        $(document).ready(function() {
            if (localStorage.getItem("dark-mode") === "enabled") {
                document.body.classList.add("dark-mode");
            }
            fetchDashboardData();
            fetchAttendanceLogs();
            setInterval(fetchAttendanceLogs, 5000);
        });
    </script>

</body>

</html>
