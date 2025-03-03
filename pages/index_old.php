<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Time In/Out</title>

    <!-- Bootstrap & Custom Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- External CSS for organization -->

    <!-- WebcamJS & jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<?php include '../includes/navbar.php'; ?>

<body>

    <div class="container mt-4">
        <div class="shadow-box text-center p-4">
            <div id="clock" aria-live="polite"></div>


            <!-- Landing Page -->
            <div id="landing-page">
                <div class="row mt-4">
                    <div class="col-md-4">
                        <h5><i class="fas fa-history text-danger"></i> Recently Timed In</h5>
                        <ul class="list-group professor-list" id="history-list" aria-live="polite"></ul>
                    </div>
                    <div class="col-md-8">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card-btn bg-primary text-white p-4" onclick="openTimeIn()">
                                    <h3><i class="fas fa-sign-in-alt"></i> Time In</h3>
                                    <button class="btn btn-light btn-lg">Proceed</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-btn bg-warning text-white p-4" onclick="openTimeOut()">
                                    <h3><i class="fas fa-sign-out-alt"></i> Time Out</h3>
                                    <button class="btn btn-light btn-lg">Proceed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time In Section -->
            <div id="time-in-section" class="hidden mt-4" style="display: none;">
                <h3>Select Professor</h3>
                <input type="text" id="search-professor-in" class="form-control" placeholder="🔍 Search Professor..." aria-label="Search Professor">
                <ul class="list-group mt-2 professor-list" id="professor-list-in">
                    <?php
                    $stmt = $conn->prepare("SELECT id, name FROM professors ORDER BY name ASC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<li class='list-group-item' onclick='selectProfessorIn({$row['id']}, \"{$row['name']}\")'>{$row['name']}</li>";
                    }
                    $stmt->close();
                    ?>
                </ul>
            </div>

            <!-- Camera Section - Initially hidden -->
            <div id="camera-section" class="hidden mt-4" style="display: none;">
                <h4>Capture Photo</h4>
                <div id="camera"></div>
                <button class="btn btn-primary mt-2" id="take-photo">📷 Take Photo</button>
            </div>

            <!-- Time Out Section -->
            <div id="time-out-section" class="hidden mt-4" style="display: none;">
                <h3>Select Professor for Time Out</h3>
                <input type="text" id="search-professor-out" class="form-control" placeholder="🔍 Search Professor..." aria-label="Search Professor">
                <ul class="list-group mt-2 professor-list" id="professor-list-out" aria-live="polite"></ul>
            </div>


            <!-- Attendance Statistics Section -->
            <div class="container mt-4">
                <h3 class="fw-bold text-center mb-3">📊 Attendance Statistics</h3>
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card p-3 bg-primary text-white">
                            <h4>Total Professors</h4>
                            <h2>
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM professors");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-3 bg-success text-white">
                            <h4>Total Attendance</h4>
                            <h2>
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM attendance");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-3 bg-warning text-white">
                            <h4>Late Entries</h4>
                            <h2>
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE status='Late'");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-Time Attendance Table -->
            <div id="attendance-section" class="mt-4">
                <h3>Recent Time Ins</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Work Duration</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-table" aria-live="polite"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function loadAttendance() {
            fetch("../api/get-attendance.php")
                .then(response => response.json())
                .then(data => {
                    let table = document.getElementById("attendance-table");
                    let historyList = document.getElementById("history-list");
                    table.innerHTML = "";
                    historyList.innerHTML = "";

                    data.forEach(row => {
                        if (row.name) { // ✅ Prevent empty row

                            // Create Google Maps link if location is available
                            let locationLink = (row.latitude && row.longitude) ?
                                `<a href="https://www.google.com/maps?q=${row.latitude},${row.longitude}" target="_blank" class="btn btn-info btn-sm">📍 View Location</a>` :
                                "N/A";

                            table.innerHTML +=
                                `<tr>
                                <td>${row.name}</td>
                                <td>${row.check_in}</td>
                                <td>${row.check_out || "Pending"}</td>
                                <td>${row.work_duration || "N/A"}</td>
                                <td>${row.recorded_at}</td>
                            </tr>`;

                            historyList.innerHTML += `<li class='list-group-item'>${row.name}</li>`;
                        }
                    });
                })
                .catch(error => console.error("Error fetching attendance:", error));
        }

        setInterval(loadAttendance, 5000);
        document.addEventListener("DOMContentLoaded", loadAttendance);

        function openTimeIn() {
            document.getElementById("landing-page").style.display = "none";
            document.getElementById("time-out-section").style.display = "none";
            document.getElementById("time-in-section").style.display = "block";
            document.getElementById("camera-section").style.display = "block"; // Show Camera Section
        }

        function openTimeOut() {
            document.getElementById("landing-page").style.display = "none";
            document.getElementById("time-in-section").style.display = "none";
            document.getElementById("camera-section").style.display = "none";
            document.getElementById("time-out-section").style.display = "block";

            // Fetch professors who have checked in but not yet checked out
            fetch("../api/get-checkedin-professors.php")
                .then(response => response.json())
                .then(data => {
                    let list = document.getElementById("professor-list-out");
                    list.innerHTML = ""; // Clear previous entries

                    // Access the checked_in_professors array properly
                    if (data.checked_in_professors.length === 0) {
                        list.innerHTML = `<li class='list-group-item text-danger'>No pending check-outs</li>`;
                    } else {
                        data.checked_in_professors.forEach(prof => {
                            list.innerHTML += `<li class='list-group-item' onclick='timeOutProfessor(${prof.id})'>${prof.name}</li>`;
                        });
                    }
                })
                .catch(error => console.error("Error fetching checked-in professors:", error));
        }


        // Select Professor for Time In - Replace list with camera on selection
        function selectProfessorIn(id, name) {
            // Hide the professor list section
            document.getElementById("time-in-section").style.display = "none";

            // Show the camera section
            document.getElementById("camera-section").style.display = "block";

            // Initialize and attach the webcam
            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach("#camera");

            // Take photo and submit attendance
            document.getElementById("take-photo").onclick = function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            let latitude = position.coords.latitude;
                            let longitude = position.coords.longitude;
                            let accuracy = position.coords.accuracy;

                            Webcam.snap(function(data_uri) {
                                fetch("../api/store-photo.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded"
                                        },
                                        body: `professor_id=${id}&image_data=${encodeURIComponent(data_uri)}&latitude=${latitude}&longitude=${longitude}`
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        alert(data.message);
                                        loadAttendance();
                                        setTimeout(() => location.reload(), 5000);
                                    });
                            });
                        },
                        function(error) {
                            alert("❌ Location Error: " + error.message);
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                } else {
                    alert("❌ Geolocation is not supported by this browser.");
                }
            };
        }

        function timeOutProfessor(professorId) {
            fetch("../api/checkout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `professor_id=${professorId}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    loadAttendance(); // ✅ Reloads table WITHOUT reloading the page
                })
                .catch(error => console.error("Error in timeout process:", error));
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
        }

        // ✅ Ensure dark mode persists on page load
        document.addEventListener("DOMContentLoaded", function() {
            if (localStorage.getItem("dark-mode") === "enabled") {
                document.body.classList.add("dark-mode");
            }
        });

        function updateClock() {
            document.getElementById("clock").innerText = new Date().toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
    </script>
</body>

</html>