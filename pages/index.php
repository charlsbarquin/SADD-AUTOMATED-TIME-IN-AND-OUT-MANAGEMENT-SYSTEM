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
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- WebcamJS & jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/main.js"></script> <!-- External JS for organization -->

    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<?php include '../includes/navbar.php'; ?>

<body>
    <!-- Time In Modal -->
    <div class="modal fade" id="timeInModal" tabindex="-1" aria-labelledby="timeInModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Professor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="search-professor-in" class="form-control mb-2" placeholder="Search professor..." readonly>
                    <ul class="list-group mt-2 professor-list" id="professor-list-in">
                        <!-- Dynamic list will be populated here -->
                    </ul>
                    <!-- Camera Section -->
                    <div id="camera-section" class="mt-3 hidden">
                        <p class="text-center">📷 Capturing Image...</p>
                        <div id="camera"></div>
                        <button id="take-photo" class="btn btn-primary w-100 mt-2">📸 Capture</button>
                    </div>
                    <!-- Location Status -->
                    <div id="location-status" class="text-center mt-3 hidden">
                        <p>📍 Getting Location...</p>
                        <small id="location-text"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Out Modal (Updated) -->
    <div class="modal fade" id="timeOutModal" tabindex="-1" aria-labelledby="timeOutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Professor to Time Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group mt-2" id="professor-list-out">
                        <!-- Dynamic list of checked-in professors will be populated here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Recent History -->
    <aside class="history-panel">
        <h5 class="history-title"><i class="fas fa-clock"></i> Recent History</h5>
        <div class="history-content">
            <ul id="recent-history-list" class="list-group"></ul>
        </div>
        <button id="view-more-btn" class="btn btn-outline-secondary w-100">View More</button>
    </aside>

    <!-- Main Dashboard -->
    <main class="dashboard" id="landing-page">
        <div class="clock-container">
            <h1 id="clock" class="fw-bold"></h1>
            <p class="text-muted">Current Time</p>
        </div>

        <div class="button-container text-center">
            <button id="time-in-btn" class="btn btn-lg text-white" style="background-color: #0099CC; border: none;" data-bs-toggle="modal" data-bs-target="#timeInModal">
                <i class="fas fa-sign-in-alt"></i> Time In
            </button>
            <button id="time-out-btn" class="btn btn-lg text-white" style="background-color: #FF6600; border: none;" data-bs-toggle="modal" data-bs-target="#timeOutModal">
                <i class="fas fa-sign-out-alt"></i> Time Out
            </button>
        </div>

        <hr class="dashboard-divider">

        <!-- Attendance Statistics -->
        <section class="stats-section">
            <h3 class="fw-bold text-center"><i class="fas fa-chart-bar"></i> Attendance Overview</h3>
            <div class="stats-container d-flex justify-content-center flex-wrap">
                <div class="stat-card total-professors">
                    <h4><i class="fas fa-user-tie"></i> Total Professors</h4>
                    <h2>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM professors");
                        echo $result->fetch_assoc()['total'];
                        ?>
                    </h2>
                </div>

                <div class="stat-card total-attendance">
                    <h4><i class="fas fa-user-check"></i> Total Attendance</h4>
                    <h2>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM attendance");
                        echo $result->fetch_assoc()['total'];
                        ?>
                    </h2>
                </div>

                <div class="stat-card pending-checkouts">
                    <h4><i class="fas fa-clock"></i> Pending Check-Outs</h4>
                    <h2>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE check_out IS NULL AND DATE(check_in) = CURDATE()");
                        echo $result->fetch_assoc()['total'];
                        ?>
                    </h2>
                </div>
            </div>
        </section>
    </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load the attendance overview when the page is ready
        loadAttendanceOverview();
        loadRecentHistory();
        loadAttendance(); // Added the loadAttendance call to load status information on page load

        // Function to update the clock (12-hour format with AM/PM)
        function updateClock() {
            let clockElement = document.getElementById("clock");
            if (clockElement) {
                let date = new Date();
                let hours = date.getHours();
                let minutes = date.getMinutes();
                let seconds = date.getSeconds();
                let ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? hours : 12; // 12:00 AM is 12, not 0
                minutes = minutes < 10 ? '0' + minutes : minutes; // Add leading zero to minutes if < 10
                seconds = seconds < 10 ? '0' + seconds : seconds; // Add leading zero to seconds if < 10

                // Display time in the format: HH:MM:SS AM/PM
                clockElement.innerText = `${hours}:${minutes}:${seconds} ${ampm}`;
            }
        }

        // Set interval to update clock every second
        setInterval(updateClock, 1000);

        // Initialize the clock immediately on page load
        updateClock();

        // Function to load attendance overview (including Pending Check-Outs)
        function loadAttendanceOverview() {
            fetch('../api/get-attendance.php') // Ensure this API returns the pending checkouts
                .then(response => response.json())
                .then(data => {
                    // Update the total professors
                    document.getElementById("total-professors").innerText = data.total_professors;

                    // Update the total attendance
                    document.getElementById("total-attendance").innerText = data.total_attendance;

                    // Update the Pending Check-Outs
                    document.getElementById("pending-checkouts").innerText = data.pending_checkouts;

                    // Update any other relevant information
                })
                .catch(error => console.error('Error fetching attendance overview:', error));
        }

        // Function to load recent history
        // Function to load recent history
        function loadRecentHistory() {
            fetch('../api/get-recent-history.php') // Endpoint to get recent history
                .then(response => response.json())
                .then(data => {
                    let historyList = document.getElementById('recent-history-list');
                    if (historyList) {
                        historyList.innerHTML = ''; // Clear previous list content
                    } else {
                        console.error('Element #recent-history-list not found.');
                        return;
                    }

                    if (data.length === 0) {
                        historyList.innerHTML = '<li>No recent history available.</li>';
                    } else {
                        data.forEach(item => {
                            let li = document.createElement('li');
                            li.classList.add('list-group-item');

                            // Determine status color
                            let statusClass = '';
                            if (item.status === 'Late') statusClass = 'late';
                            else if (item.status === 'On Leave') statusClass = 'leave';

                            // Add content
                            li.innerHTML = `
                        <span class="name">${item.name}</span> 
                        - 
                        <span class="status ${statusClass}">${item.status}</span> 
                        <span class="entry-details">at ${item.check_in} 
                        ${item.location ? `- Location: ${item.location}` : ''}</span>
                    `;
                            historyList.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching recent history:', error);
                    let historyList = document.getElementById('recent-history-list');
                    if (historyList) {
                        historyList.innerHTML = '<li>Failed to load recent history.</li>';
                    }
                });
        }

        // Function to load attendance information
        function loadAttendance() {
            fetch("../api/get-attendance.php")
                .then(response => response.json())
                .then(data => {
                    console.log("Attendance Data:", data); // Add logging to check what the backend is returning
                    let table = document.getElementById("attendance-table");
                    table.innerHTML = "";

                    data.forEach(row => {
                        table.innerHTML += `
                    <tr>
                        <td><img src="${row.face_scan_image}" width="50"></td>
                        <td>${row.name}</td>
                        <td>${row.check_in}</td>
                        <td>${row.check_out ? row.check_out : `<button class="btn btn-sm btn-danger timeout-btn" data-id="${row.professor_id}">Time Out</button>`}</td>
                        <td>${row.recorded_at}</td>
                        <td><a href="https://www.google.com/maps?q=${row.latitude},${row.longitude}" target="_blank">📍 View Location</a></td>
                        <td>${row.status}</td> <!-- Display status -->
                    </tr>
                `;
                    });

                    document.querySelectorAll(".timeout-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            const professorId = this.getAttribute("data-id");
                            checkOutProfessor(professorId);
                        });
                    });
                })
                .catch(error => console.error("Error:", error));
        }

        // Handle "View More" Button (Optional - can be expanded)
        document.getElementById('view-more-btn').addEventListener('click', function() {
            alert('Fetching more data...');
        });

        // Handle Time Out Button Click
        document.getElementById('time-out-btn').addEventListener('click', function() {
            openTimeOut(); // This opens the modal and loads checked-in professors
        });

        // Function to open Time Out modal and fetch checked-in professors
        function openTimeOut() {
            fetch('../api/get-checkedin-professors.php') // API endpoint to get professors who have checked in
                .then(response => response.json())
                .then(data => {
                    let professorListOut = document.getElementById('professor-list-out');
                    professorListOut.innerHTML = ''; // Clear previous list content

                    if (data.length === 0) {
                        professorListOut.innerHTML = '<li class="list-group-item">No professors available for check-out.</li>';
                    } else {
                        data.forEach(prof => {
                            let li = document.createElement('li');
                            li.classList.add('list-group-item');
                            li.setAttribute('data-id', prof.id);
                            li.textContent = prof.name;

                            // Add event listener to check out the professor
                            li.addEventListener('click', function() {
                                checkOutProfessor(prof.id, prof.name);
                            });

                            professorListOut.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching checked-in professors:', error);
                    let professorListOut = document.getElementById('professor-list-out');
                    if (professorListOut) {
                        professorListOut.innerHTML = '<li class="list-group-item">Failed to load checked-in professors.</li>';
                    } else {
                        console.error('Element #professor-list-out not found.');
                    }
                });
        }

        // Handle Professor Check-Out
        function checkOutProfessor(id, name) {
            fetch('../api/checkout.php', { // API endpoint to process Time Out
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `professor_id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response from checkout.php:', data); // Log the response from the backend
                    alert(data.message); // Display success or error message
                    if (data.status === 'success') {
                        console.log(`Professor ${data.professor_name} checked out at ${data.check_out}`);
                        loadRecentHistory(); // Reload history after time-out to show latest status
                        // Auto-refresh the page after 3 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 3000); // 3000 milliseconds = 3 seconds
                    }
                })
                .catch(error => {
                    console.error('Error checking out professor:', error);
                    alert('Error during time out process.');
                });
        }

        // Handle Time In Button Click
        document.getElementById('time-in-btn').addEventListener('click', function() {
            fetchProfessors();
        });

        // Fetch Professors from Backend for Time In
        function fetchProfessors() {
            fetch('../api/get-professors.php') // API endpoint to fetch professors
                .then(response => response.json())
                .then(data => {
                    let professorList = document.getElementById('professor-list-in');
                    professorList.innerHTML = ''; // Clear any existing list items

                    if (data.length === 0) {
                        professorList.innerHTML = '<li class="list-group-item">No professors available.</li>';
                    } else {
                        data.forEach(prof => {
                            let li = document.createElement('li');
                            li.classList.add('list-group-item', 'professor-item');
                            li.setAttribute('data-id', prof.id);
                            li.textContent = prof.name;

                            li.addEventListener('click', function() {
                                selectProfessor(prof.id, prof.name);
                            });

                            professorList.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching professors:', error);
                    let professorList = document.getElementById('professor-list-in');
                    professorList.innerHTML = '<li class="list-group-item">Failed to load professors.</li>';
                });
        }

        // Handle professor selection for Time In
        function selectProfessor(id, name) {
            document.getElementById('search-professor-in').value = name; // Set selected name in the search box
            document.getElementById('camera-section').classList.remove('hidden'); // Show camera section
            document.getElementById('location-status').classList.remove('hidden'); // Show location status

            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach('#camera');

            document.getElementById('take-photo').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let latitude = position.coords.latitude;
                        let longitude = position.coords.longitude;

                        Webcam.snap(function(data_uri) {
                            fetch('../api/store-photo.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `professor_id=${id}&image_data=${encodeURIComponent(data_uri)}&latitude=${latitude}&longitude=${longitude}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                    loadRecentHistory(); // Refresh attendance after check-in
                                    // Auto-refresh the page after 3 seconds
                                    setTimeout(() => {
                                        location.reload();
                                    }, 3000); // 3000 milliseconds = 3 seconds
                                });
                        });
                    });
                } else {
                    alert('Geolocation not supported');
                }
            });
        }
    });
</script>
</body>

</html>