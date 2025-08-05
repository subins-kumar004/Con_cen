<?php
include 'php/db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all bookings to display
$bookings_query = "SELECT b.id, u.username, a.name AS auditorium, b.event_date, b.catering_option, b.status FROM bookings b
                   JOIN users u ON b.user_id = u.id
                   JOIN auditoriums a ON b.auditorium_id = a.id";
$bookings_result = $conn->query($bookings_query);

// Ensure no output before this point
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Logout button styles */
        .logout-btn {
            display: inline-block;
            background-color: red;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Welcome Admin !!!</h1>
        </div>

        <!-- Admin Actions -->
        <div class="admin-actions">
            <a href="manage_bookings.php" class="btn">Batch Edit All Bookings</a>
        </div>

        <!-- All Bookings Table -->
        <h2>All Bookings</h2>
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Auditorium</th>
                    <th>Event Date</th>
                    <th>Catering</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($bookings_result->num_rows > 0) {
                    while ($row = $bookings_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['auditorium']}</td>
                                <td>{$row['event_date']}</td>
                                <td>{$row['catering_option']}</td>
                                <td>{$row['status']}</td>
                                <td><a href='update_booking_status.php?id={$row['id']}' class='action-btn'>Update Status</a></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Logout button -->
    <a href="logout.php" class="logout-btn">Logout</a>
</body>
</html>

<?php
$conn->close();
?>
