<?php
include 'php/db.php';

if (isset($_GET['auditorium_id'])) {
    $auditorium_id = intval($_GET['auditorium_id']);
    $query = "SELECT event_date FROM bookings WHERE auditorium_id = ? AND status = 'approved'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $auditorium_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $unavailable_dates = [];
    while ($row = $result->fetch_assoc()) {
        $unavailable_dates[] = $row['event_date'];
    }
    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($unavailable_dates);
    exit();
}
