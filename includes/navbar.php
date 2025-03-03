<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar | Bicol University Polangui</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts (Poppins) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">

    <style>
        /* ===== General Navbar Styling ===== */
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 85px;
            transition: background-color 0.3s ease-in-out;
        }

        .navbar {
            width: 100%;
            background-color: white;
            border-bottom: 4px solid #0099CC;
            /* Aqua Blue */
            padding: 15px 25px;
            transition: all 0.3s ease-in-out;
        }

        /* ===== Logo & Title Styling ===== */
        .navbar-brand {
            display: flex;
            align-items: center;
            font-size: 22px;
            font-weight: 700;
            color: #0099CC;
            transition: transform 0.2s ease-in-out;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand img {
            height: 70px;
            /* Reduced size for better mobile responsiveness */
            width: auto;
            margin-right: 5px;
            /* Reduce spacing between logos */
        }

        .title-text {
            font-size: 18px;
            font-weight: 600;
            line-height: 1.3;
            margin-left: 10px;
            /* Ensure text is spaced correctly from logos */
        }

        /* ===== Navigation Items ===== */
        .navbar-nav .nav-link {
            font-size: 18px;
            font-weight: 500;
            color: black;
            transition: all 0.3s ease-in-out;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white;
            background-color: #0099CC;
            /* Aqua Blue */
        }

        /* ===== Dropdown Styling ===== */
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
            font-size: 16px;
        }

        .dropdown-item {
            padding: 12px 18px;
            transition: all 0.3s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: #0099CC;
            /* Aqua Blue */
            color: white;
        }

        /* ===== Dark Mode ===== */
        .dark-mode {
            background-color: #121212;
            color: white;
        }

        .dark-mode .navbar {
            background-color: #1c1c1c;
            border-bottom: 4px solid #FF6600;
            /* Orange */
        }

        .dark-mode .navbar-nav .nav-link {
            color: white;
        }

        .dark-mode .navbar-nav .nav-link:hover {
            background-color: #FF6600;
            color: white;
        }

        .dark-mode .dropdown-menu {
            background-color: #1c1c1c;
            color: white;
        }

        .dark-mode .dropdown-item:hover {
            background-color: #333;
        }

        /* ===== Icons & Notifications ===== */
        .nav-icons {
            font-size: 22px;
            color: black;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .nav-icons:hover {
            color: #0099CC;
            transform: scale(1.1);
        }

        .dark-mode .nav-icons {
            color: white;
        }

        .notification-badge {
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 3px 6px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* ===== Animations ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== Mobile and Tablet Responsiveness ===== */

        /* Ensure that navbar items stack on smaller screens */
        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                font-size: 16px;
                padding: 8px 12px;
            }

            .navbar-brand {
                flex-direction: column;
                text-align: center;
            }

            .navbar-brand img {
                height: 60px;
                margin-right: 0;
                margin-bottom: 10px;
            }

            .title-text {
                font-size: 16px;
                text-align: center;
            }

            /* Mobile menu toggle */
            .navbar-toggler {
                border: none;
            }

            .navbar-collapse {
                margin-top: 10px;
            }

            .dropdown-menu {
                text-align: center;
            }
        }

        /* Ensure that menu items stay stacked and responsive on small screens */
        @media (max-width: 480px) {
            .navbar-nav .nav-link {
                font-size: 14px;
                padding: 8px 10px;
            }

            .navbar-collapse {
                margin-top: 10px;
            }

            .dropdown-menu {
                width: 100%;
            }
        }
    </style>
</head>

<>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!-- Left: Logos & System Title -->
            <a class="navbar-brand" href="index.php">
                <div class="logo-container">
                    <img src="../assets/images/bu-logo.png" alt="BU Logo">
                    <img src="../assets/images/polangui-logo.png" alt="Polangui Logo">
                </div>
                <span class="title-text">
                    <span style="color: #0099CC;">Bicol University</span>
                    <span style="color: #FF6600;">Polangui</span><br>
                    <span style="color: black;">CSD Professors' Attendance System</span>
                </span>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="fas fa-bars"></span>
            </button>

            <!-- Navbar Items -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../pages/index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/attendance-report.php"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/dashboard.php"><i class="fas fa-chart-line"></i> Statistics</a></li>

                    <!-- Settings Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog nav-icons"></i> Settings
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                            <li><a class="dropdown-item" href="#" id="darkModeToggle"><i class="fas fa-moon"></i> Dark Mode</a></li>
                            <li><a class="dropdown-item" href="../pages/settings.php"><i class="fas fa-sliders-h"></i> System Settings</a></li>
                        </ul>
                    </li>

                    <!-- Admin Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle nav-icons"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="../pages/profile.php"><i class="fas fa-user"></i> View Profile</a></li>
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell nav-icons"></i>
                            <span id="notificationCount" class="notification-badge" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <div id="notificationList">
                                <li class="dropdown-item text-center text-muted">No new notifications</li>
                            </div>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dark Mode & Language Selector Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const darkModeToggle = document.getElementById("darkModeToggle");
            const body = document.body;
            const languageDropdown = document.getElementById("selectedLanguage");
            const languageOptions = document.querySelectorAll(".language-option");

            // ✅ Apply saved Dark Mode setting on page load
            if (localStorage.getItem("dark-mode") === "enabled") {
                body.classList.add("dark-mode");
                darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
            }

            // ✅ Toggle Dark Mode
            darkModeToggle.addEventListener("click", function() {
                body.classList.toggle("dark-mode");
                const isDarkMode = body.classList.contains("dark-mode");
                localStorage.setItem("dark-mode", isDarkMode ? "enabled" : "disabled");
                darkModeToggle.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i> Light Mode' : '<i class="fas fa-moon"></i> Dark Mode';
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const notificationList = document.getElementById("notificationList");
            const notificationBadge = document.getElementById("notificationCount");

            function fetchNotifications() {
                fetch("../api/fetch-notifications.php")
                    .then(response => response.json())
                    .then(notifications => {
                        notificationList.innerHTML = ""; // Clear previous notifications

                        if (notifications.length === 0) {
                            notificationList.innerHTML = `<li class="dropdown-item text-center text-muted">No new notifications</li>`;
                            notificationBadge.style.display = "none";
                        } else {
                            notifications.forEach((notif) => {
                                let notificationItem = document.createElement("li");
                                notificationItem.classList.add("dropdown-item", "notification-item");
                                notificationItem.innerHTML = `
                            <strong>${notif.message}</strong>
                            <br><small class="text-muted">${notif.created_at}</small>
                        `;
                                notificationItem.onclick = function() {
                                    markNotificationAsRead(notif.id);
                                };
                                notificationList.appendChild(notificationItem);
                            });

                            // Update badge count
                            notificationBadge.innerText = notifications.length;
                            notificationBadge.style.display = "inline-block";
                        }
                    })
                    .catch(error => console.error("Error fetching notifications:", error));
            }

            // Function to mark notifications as read
            function markNotificationAsRead(notificationId) {
                fetch("../api/mark-notification-read.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `notification_id=${notificationId}`
                }).then(() => fetchNotifications()); // Refresh notifications after marking as read
            }

            // Fetch notifications every 5 seconds (real-time updates)
            setInterval(fetchNotifications, 5000);
            fetchNotifications();
        });
    </script>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>