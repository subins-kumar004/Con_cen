<?php
session_start();
include 'php/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch auditoriums with capacities
$query_auditoriums = "SELECT id, name, capacity FROM auditoriums";
$result_auditoriums = $conn->query($query_auditoriums);

if ($result_auditoriums === false) {
    die("Error fetching auditoriums: " . $conn->error);
}

$booking_success = false;

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auditorium'], $_POST['event_date'], $_POST['catering_option'])) {
    $auditorium_id = $_POST['auditorium'];
    $event_date = $_POST['event_date'];
    $catering_option = $_POST['catering_option'];
    $user_id = $_SESSION['user_id'];

    // Check if the selected event date is already booked
    $query_unavailable_dates = "SELECT event_date FROM bookings WHERE auditorium_id = ? AND event_date = ? AND status = 'approved'";
    $stmt = $conn->prepare($query_unavailable_dates);
    $stmt->bind_param("is", $auditorium_id, $event_date);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('The selected date is unavailable. Please choose another date.');</script>";
    } else {
        // Prepare SQL query to insert the booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, auditorium_id, event_date, catering_option, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind the parameters and execute the query
        $stmt->bind_param("iiss", $user_id, $auditorium_id, $event_date, $catering_option);
        if ($stmt->execute()) {
            $booking_success = true;
        } else {
            echo "Error submitting booking: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Auditorium</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <?php if ($booking_success): ?>
    <!-- Meta refresh tag for delayed redirection after 2 seconds -->
    <meta http-equiv="refresh" content="2;url=welcome.php">
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const auditoriumSelect = document.getElementById('auditorium');
            const dateInput = document.getElementById('event_date');
            const cateringSelect = document.getElementById('catering_option');
            const cateringDetails = document.getElementById('catering_details');
            let unavailableDates = [];

            // Disable date input initially
            dateInput.disabled = true;

            // Enable date input and fetch unavailable dates when an auditorium is selected
            auditoriumSelect.addEventListener('change', function () {
                if (auditoriumSelect.value) {
                    dateInput.disabled = false;
                    fetchUnavailableDates(auditoriumSelect.value);
                } else {
                    dateInput.disabled = true;
                    unavailableDates = [];
                }
            });

            // Update catering details dynamically based on selection
            cateringSelect.addEventListener('change', function () {
                updateCateringDetails(cateringSelect.value);
            });

            // Function to fetch unavailable dates for the selected auditorium
            function fetchUnavailableDates(auditoriumId) {
                fetch('fetch_unavailable_dates.php?auditorium_id=' + auditoriumId)
                    .then(response => response.json())
                    .then(data => {
                        unavailableDates = data;
                        checkForUnavailableDate(); // Check initially if selected date is unavailable
                    })
                    .catch(error => console.error('Error fetching unavailable dates:', error));
            }

            // Disable unavailable dates in the calendar and show an alert
            dateInput.addEventListener('input', function () {
                checkForUnavailableDate();
            });

            function checkForUnavailableDate() {
                const selectedDate = dateInput.value;
                if (unavailableDates.includes(selectedDate)) {
                    alert("The selected date is unavailable. Please choose another date.");
                    dateInput.value = ""; // Clear the selected date
                }
            }

            // Function to update catering details based on the selected option
            function updateCateringDetails(option) {
                let details = '';
                switch (option) {
                    case 'Nadan Oonu':
                        details = ` 
                            <h3>Nadan Oonu</h3>
                            <img src='assets/images/nadan_oonu.jpg' alt='Nadan Oonu' style='width:200px;'>
                            <p>Traditional Kerala feast served on a banana leaf. Includes rice, curries, pickles, papad, and desserts.</p>
                            <p><strong>Price:</strong> ₹350 per plate</p>
                        `;
                        break;
                    case 'Biriyani':
                        details = `
                            <h3>Biriyani</h3>
                            <img src='assets/images/biriyani.jpg' alt='Biriyani' style='width:200px;'>
                            <p>Flavored rice dish served with accompaniments. Available in Chicken, Beef, or Mutton options.</p>
                            <p><strong>Prices:</strong> Chicken - ₹250 per plate, Beef - ₹500 per plate, Mutton - ₹500 per plate</p>
                        `;
                        break;
                    case 'Chinese':
                        details = `
                            <h3>Chinese Cuisine</h3>
                            <img src='assets/images/chinese.jpg' alt='Chinese' style='width:200px;'>
                            <p>A delightful spread of noodles, fried rice, and Manchurian dishes with exotic flavors.</p>
                            <p><strong>Price:</strong> ₹500 per plate</p>
                        `;
                        break;
                    default:
                        details = '';
                }
                cateringDetails.innerHTML = details;
            }
        });
    </script>
</head>
<body>
    <h2>Book an Auditorium</h2>

    <?php if ($booking_success): ?>
        <p>Booking request submitted successfully! You will be redirected to the welcome page shortly.</p>
    <?php endif; ?>

    <form action="book_auditorium.php" method="post">
        <label for="auditorium">Select Auditorium:</label>
        <select name="auditorium" id="auditorium" required>
            <option value="">Choose an Auditorium</option>
            <?php
            while ($row_auditorium = $result_auditoriums->fetch_assoc()) {
                echo "<option value='" . $row_auditorium['id'] . "'>" . htmlspecialchars($row_auditorium['name']) . 
                     " (Capacity: " . $row_auditorium['capacity'] . ")</option>";
            }
            ?>
        </select><br>

        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" id="event_date" required> <br>

        <label for="catering_option">Select Catering Option:</label>
        <select name="catering_option" id="catering_option">
            <option value="">No Catering</option>
            <option value="Nadan Oonu">Nadan Oonu</option>
            <option value="Biriyani">Biriyani</option>
            <option value="Chinese">Chinese</option>
        </select><br>

        <div id="catering_details" style="margin-top: 20px;"></div><br>

        <button type="submit">Book Now</button>
    </form>

    <a href="welcome.php">Back to Welcome Page</a>
</body>
</html>
