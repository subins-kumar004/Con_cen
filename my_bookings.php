<?php
include 'php/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's bookings
$stmt = $conn->prepare("SELECT b.id, a.name AS auditorium, b.event_date, b.catering_option, b.status FROM bookings b JOIN auditoriums a ON b.auditorium_id = a.id WHERE b.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>My Bookings</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Auditorium</th>
                <th>Event Date</th>
                <th>Catering Option</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['auditorium'] ?></td>
                    <td><?= $row['event_date'] ?></td>
                    <td><?= $row['catering_option'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><a href="cancel_booking.php?id=<?= $row['id'] ?>">Cancel</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You don't have any bookings yet.</p>
    <?php endif; ?>
<br>
    <a href="welcome.php" class="btn">Go back to Welcome Page</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>