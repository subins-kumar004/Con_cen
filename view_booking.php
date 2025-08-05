<?php
include 'php/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the booking ID from the query string
if (!isset($_GET['booking_id'])) {
    echo "<script>alert('Invalid booking ID.'); window.location.href = 'welcome.php';</script>";
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Fetch booking details for the specific booking
$query = "SELECT b.event_date, b.catering_option, b.status, b.created_at, a.name as auditorium_name 
          FROM bookings b
          JOIN auditoriums a ON b.auditorium_id = a.id
          WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$stmt->store_result();

// Check if the booking exists
if ($stmt->num_rows > 0) {
    $stmt->bind_result($event_date, $catering_option, $status, $created_at, $auditorium_name);
    $stmt->fetch();
} else {
    echo "<script>alert('Booking not found or you do not have permission to view this booking.'); window.location.href = 'welcome.php';</script>";
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #197b30;
            color: white;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #197b30;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #145c24;
        }

        .cancel-btn {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Booking Details</h1>
    <table>
        <tr>
            <th>Auditorium</th>
            <td><?php echo htmlspecialchars($auditorium_name); ?></td>
        </tr>
        <tr>
            <th>Event Date</th>
            <td><?php echo htmlspecialchars($event_date); ?></td>
        </tr>
        <tr>
            <th>Catering Option</th>
            <td><?php echo htmlspecialchars($catering_option); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo ucfirst($status); ?></td>
        </tr>
        <tr>
            <th>Booking Date</th>
            <td><?php echo htmlspecialchars($created_at); ?></td>
        </tr>
    </table>

    <form action="cancel_booking.php" method="POST">
        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
        <button type="submit" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</button>
    </form>

    <a href="welcome.php" class="btn">Back to Welcome Page</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
