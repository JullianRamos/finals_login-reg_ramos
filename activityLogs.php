<?php
session_start(); // Start the session to manage user authentication
include 'core/models.php'; // Include model functions for database interactions

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// If not logged in, redirect to login page with an access denied message
if (!$is_logged_in) {
    echo "Access Denied. You do not have permission to view activity logs.";
    exit;
}

// Get the current logged-in user details
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch activity logs for the logged-in user
$activity_logs = getActivityLogs($user_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <style>
        /* Base styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        header {
            background-color: #0044cc;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        main {
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0044cc;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #e0f7fa;
        }

        footer {
            background-color: #0044cc;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Activity Logs</h1>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h2>Activity Logs for <?php echo htmlspecialchars($username); ?></h2>

            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($activity_logs)) {
                        foreach ($activity_logs as $log) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
                            echo "<td>" . htmlspecialchars($log['action']) . "</td>";
                            echo "<td>" . htmlspecialchars($log['description']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No activity logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        &copy; 2024 Applicants Management System. All rights reserved.
    </footer>
</body>
</html>
