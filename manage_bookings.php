<?php
include 'php/db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all bookings
$query = "SELECT b.id, u.username, a.name AS auditorium, b.event_date, b.catering_option, b.status 
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN auditoriums a ON b.auditorium_id = a.id";
$bookings_result = $conn->query($query);

// Update booking status if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    // Update status query
    $update_stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $booking_id);
    if ($update_stmt->execute()) {
        echo "<p>Booking status updated successfully.</p>";
        header("refresh:2;url=manage_bookings.php");
    } else {
        echo "<p>Error updating status: " . $conn->error . "</p>";
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Manage Bookings</h1>
        
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
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <!-- Form to update the booking status -->
                        <form method="POST" action="">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <select name="status" required>
                                <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="Rejected" <?php echo ($row['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit" class="btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back to Admin Panel Button -->
        <a href="admin_panel.php" class="btn">Go back to Admin Panel</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>