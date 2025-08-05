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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>View All Bookings</h1>
            <a href="admin_panel.php" class="logout-btn">Back to Admin Panel</a>
        </div>

        <!-- All Bookings Table -->
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
                <?php while ($row = $bookings_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['auditorium']; ?></td>
                    <td><?php echo $row['event_date']; ?></td>
                    <td><?php echo $row['catering_option']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <a href="update_booking_status.php?id=<?php echo $row['id']; ?>" class="action-btn">Update Status</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>