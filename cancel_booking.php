<?php
include 'php/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['booking_id'])) {
        echo "<script>alert('Invalid booking ID.'); window.location.href = 'welcome.php';</script>";
        exit();
    }

    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    // Delete the booking
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Booking canceled successfully.'); window.location.href = 'welcome.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to cancel the booking.'); window.location.href = 'welcome.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error: Could not prepare statement.'); window.location.href = 'welcome.php';</script>";
    }
} else {
    header("Location: welcome.php");
    exit();
}

$conn->close();
?>
