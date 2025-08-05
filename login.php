<?php
include 'php/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query to check login credentials
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify password
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store user details in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: welcome.php");
            }
            exit();
        } else {
            echo "<script>alert('SSS EVENTS: Invalid password. Try again.');</script>";
        }
    } else {
        echo "<script>alert('SSS EVENTS: Invalid username. Try again.');</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style1.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: rgba(0, 0, 0, 0.8);  /* Black background on the sides */
            color: #f4f4f4; /* Light text color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url("./assets/images/logo.jpeg") no-repeat center center;
            background-size: cover; /* Keeps the image's original size */
        }

        .container1 {
            background: rgba(22, 36, 71, 0.8); /* Dark blue with transparency */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #f4f4f4; /* Light text */
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #dcdde1; /* Muted light text */
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            background: #0f3460; /* Very dark blue */
            color: #f4f4f4;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #a4b0be; /* Light muted placeholder */
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #e94560; /* Bright red button */
            color: #f4f4f4;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #f05968; /* Lighter red on hover */
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            color: #4cd137; /* Green link */
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container1">
        <h2>Login</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter your username" required>
           
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>
           
            <button type="submit">Login</button>
        </form>
        <div class="link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>