<?php
include 'php/db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the booking ID is provided
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Fetch the current status and other details of the booking
    $query = "SELECT b.id, b.event_date, b.status, a.name as auditorium_name, b.catering_option
              FROM bookings b
              JOIN auditoriums a ON b.auditorium_id = a.id
              WHERE b.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    // If the booking doesn't exist, redirect to the admin panel
    if (!$booking) {
        header("Location: admin_panel.php");
        exit();
    }

    // Update booking status if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_status = $_POST['status'];

        // Prepare SQL query to update the status of the booking
        $update_stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $booking_id);
        if ($update_stmt->execute()) {
            echo "<p>Booking status updated to $new_status.</p>";
            // Redirect back to the admin panel after the update
            header("refresh:2;url=admin_panel.php");
        } else {
            echo "<p>Error updating status: " . $conn->error . "</p>";
        }
        $update_stmt->close();
    }
} else {
    // If no booking ID is provided, redirect to the admin panel
    header("Location: admin_panel.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking Status</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1>Update Booking Status</h1>

<!-- Display booking details -->
<?php if ($booking): ?>
    <p><strong>Booking ID:</strong> <?php echo $booking['id']; ?></p>
    <p><strong>Auditorium:</strong> <?php echo $booking['auditorium_name']; ?></p>
    <p><strong>Event Date:</strong> <?php echo $booking['event_date']; ?></p>
    <p><strong>Catering Option:</strong> <?php echo $booking['catering_option']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></p>

    <!-- Form to update the status -->
    <form method="POST" action="">
        <label for="status">Update Status:</label>
        <select name="status" id="status" required>
            <option value="Pending" <?php echo ($booking['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Approved" <?php echo ($booking['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
            <option value="Rejected" <?php echo ($booking['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
        </select>

        <button type="submit" class="btn">Update Status</button>
    </form>
<?php endif; ?>

<!-- Back to Admin Panel Button -->
<a href="admin_panel.php" class="btn">Go back to Admin Panel</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>