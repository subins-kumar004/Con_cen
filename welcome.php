<?php
include 'php/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

// Check if the user exists in the database
if ($stmt->num_rows > 0) {
    $stmt->bind_result($username, $role);
    $stmt->fetch();
} else {
    // Redirect to login page if the user does not exist
    header("Location: login.php");
    exit();
}
$stmt->close();

// Fetch upcoming bookings for the user
$booking_query = "SELECT b.id, b.event_date, b.catering_option, b.status, a.name as auditorium_name
                  FROM bookings b
                  JOIN auditoriums a ON b.auditorium_id = a.id
                  WHERE b.user_id = ? AND b.event_date >= CURDATE()
                  ORDER BY b.event_date ASC";
$booking_stmt = $conn->prepare($booking_query);
$booking_stmt->bind_param("i", $user_id);
$booking_stmt->execute();
$booking_stmt->bind_result($booking_id, $event_date, $catering_option, $status, $auditorium_name);
$booking_stmt->store_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 5px;
        }
        h1, h2 {
            text-align: center;
        }
        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        ul li {
            margin: 10px 0;
        }
        ul li a {
            text-decoration: none;
            color: blue;
        }

        /* Container styles */
        .options-container {
            background-color: rgba(232, 119, 119, 0.8); /* Semi-transparent white */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin: auto;
            width: 80%;
            max-width: 600px;
        }

        /* Table styles */
        .upcoming-bookings table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .upcoming-bookings th, .upcoming-bookings td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .upcoming-bookings th {
            background-color: rgb(24, 122, 20);
            color: white;
        }

        /* Status colors */
        .status-approved {
            color: green;
        }
        .status-rejected {
            color: red;
        }
        .status-pending {
            color: orange;
        }

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

        /* View More button styles */
        .view-more-btn {
            color: blue;
            text-decoration: none;
            font-weight: bold;
        }

        .view-more-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

<!-- Display different options based on user role -->
<?php if ($role == 'admin'): ?>
    <div class="options-container">
        <h2>Admin Options</h2>
        <ul>
            <li><a href="admin_panel.php">Admin Panel - Manage Bookings</a></li>
            <li><a href="user_profile.php">User Profile (Coming Soon)</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>
<?php else: ?>
    <div class="options-container">
        <h2>User Options</h2>
        <ul>
            <li><a href="book_auditorium.php">Book an Auditorium</a></li>
            <li><a href="user_profile.php">User Profile (Coming Soon)</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>

        <!-- Display upcoming bookings for the user -->
        <div class="upcoming-bookings">
            <h3>Your Upcoming Bookings</h3>
            <?php if ($booking_stmt->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Auditorium</th>
                            <th>Event Date</th>
                            <th>Catering Option</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking_stmt->fetch()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($auditorium_name); ?></td>
                                <td><?php echo htmlspecialchars($event_date); ?></td>
                                <td><?php echo htmlspecialchars($catering_option); ?></td>
                                <td class="<?php echo 'status-' . strtolower($status); ?>">
                                    <?php
                                    if (strtolower($status) === 'approved') {
                                        echo 'Approved';
                                    } elseif (strtolower($status) === 'rejected') {
                                        echo 'Rejected';
                                    } elseif (strtolower($status) === 'pending') {
                                        echo 'Pending';
                                    } else {
                                        echo 'Unknown';
                                    }
                                    ?>
                                </td>
                                <td><a href="view_booking.php?booking_id=<?php echo $booking_id; ?>" class="view-more-btn">View More</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no upcoming bookings.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

</body>
</html>

<?php
$booking_stmt->close();
$conn->close();
?>
