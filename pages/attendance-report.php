<?php
include '../config/database.php';
require_once('../tcpdf/tcpdf.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            transition: background-color 0.3s ease;
        }

        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .filter-card {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-card .row {
            padding: 10px;
        }

        .filter-card select,
        .filter-card input {
            margin: 5px;
            padding: 8px;
            border-radius: 5px;
            width: 100%;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
        }

        .table .badge {
            border-radius: 5px;
        }

        .badge.bg-danger {
            background-color: #dc3545;
        }

        .badge.bg-warning {
            background-color: #ffc107;
        }

        .floating-action-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .floating-action-button:hover {
            background-color: #0056b3;
        }

        .table .row-highlight {
            background-color: #f0f8ff;
        }

        .download-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .download-buttons .btn {
            font-size: 16px;
            padding: 10px 20px;
        }

        .dark-mode .filter-card {
            background-color: #1e1e1e;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.2);
        }

        .dark-mode .table {
            background-color: #333;
            border: 1px solid #444;
        }

        .dark-mode .table thead {
            background-color: #444;
        }

        .dark-mode .table th,
        .dark-mode .table td {
            color: #ddd;
        }
    </style>
</head>

<?php include '../includes/navbar.php'; ?>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">📊 Attendance Report</h1>

        <!-- Filter Options -->
        <div class="filter-card">
            <div class="row">
                <div class="col-md-4">
                    <label for="filter-date">📅 Select Date:</label>
                    <input type="date" id="filter-date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="filter-professor">🏫 Select Professor:</label>
                    <select id="filter-professor" class="form-control">
                        <option value="">All Professors</option>
                        <?php
                        $professors = $conn->query("SELECT DISTINCT name FROM professors ORDER BY name ASC");
                        while ($row = $professors->fetch_assoc()) {
                            echo "<option value='{$row['name']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-status">📊 Attendance Status:</label>
                    <select id="filter-status" class="form-control">
                        <option value="">All</option>
                        <option value="Present">Present</option>
                        <option value="Late">Late</option>
                        <option value="Absent">Absent</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <table class="table table-bordered" id="attendance-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Work Duration</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT a.*, p.name 
                FROM attendance a
                JOIN professors p ON a.professor_id = p.id
                ORDER BY a.checkin_date DESC";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $timeOutDisplay = $row['check_out'] ? $row['check_out'] : "<span class='badge bg-danger'>Pending</span>";

                    echo "<tr class='row-highlight'>
                    <td>{$row['name']}</td>
                    <td>{$row['check_in']}</td>
                    <td>{$timeOutDisplay}</td>
                    <td>{$row['work_duration']}</td>
                    <td>{$row['checkin_date']}</td>
                    <td><span class='badge bg-warning'>{$row['status']}</span></td>
                  </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Download Buttons -->
        <div class="download-buttons"></div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

        <script>
            $(document).ready(function() {
                // Destroy any previous DataTable instances to avoid conflicts
                if ($.fn.DataTable.isDataTable('#attendance-table')) {
                    $('#attendance-table').DataTable().destroy();
                }

                // Initialize DataTable without search and pagination
                var table = $('#attendance-table').DataTable({
                    searching: false, // Disable search bar
                    paging: false, // Disable pagination
                    dom: 'Bfrtip', // Keep buttons intact
                    buttons: [{
                            extend: 'csvHtml5',
                            text: '📥 Download CSV',
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5] // Export specific columns
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '📥 Download PDF',
                            className: 'btn btn-danger',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5] // Export specific columns
                            }
                        }
                    ],
                    language: {
                        info: "" // Remove the "Showing x to y of z entries" text
                    }
                });

                // Move the export buttons to the download-buttons container
                table.buttons().container().appendTo('.download-buttons');

                // Apply Filters for Date, Professor, and Status
                $("#filter-date, #filter-professor, #filter-status").on("change", function() {
                    var date = $("#filter-date").val();
                    var professor = $("#filter-professor").val();
                    var status = $("#filter-status").val();

                    // Apply the search filters to the DataTable
                    table
                        .column(4).search(date) // Date column (index 4)
                        .column(0).search(professor) // Name column (index 0)
                        .column(5).search(status) // Status column (index 5)
                        .draw(); // Redraw the table with the applied filters
                });

                // Dark Mode Toggle Functionality
                function toggleDarkMode() {
                    document.body.classList.toggle('dark-mode');
                    localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
                }

                // Check Dark Mode Status on Load
                document.addEventListener('DOMContentLoaded', function() {
                    if (localStorage.getItem('dark-mode') === 'enabled') {
                        document.body.classList.add('dark-mode');
                    }
                });
            });
        </script>

</body>

</html>