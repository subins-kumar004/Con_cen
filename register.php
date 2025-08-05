<?php
include 'php/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $role = 'user'; // Default role (change if necessary)
    $created_at = date("Y-m-d H:i:s");

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, created_at) VALUES (?, ?, ?, ?, ?)");
    
    // Check if the prepare statement was successful
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters (s = string for each)
    $stmt->bind_param("sssss", $username, $hashed_password, $email, $role, $created_at);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        input[type="email"],
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
        input[type="email"]::placeholder,
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
        <h2>Register</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Register</button>
        </form>
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
