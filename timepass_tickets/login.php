<?php
session_start();
include_once "connection.php"; // Ensure this file correctly connects to your database

// Enable error reporting for debugging (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare the SQL statement securely
        $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE email = ?");
        if (!$stmt) {
            die("SQL error: " . $conn->error); // Debugging error
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Timepass Tickets</title>
    <link rel="stylesheet" href="styles.css"> <!-- Linking your styles.css file -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .container {
            position: relative;
            z-index: 2;
            background-color: rgba(0, 0, 0, 0.85); /* Match your .card styling */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 255, 213, 0.5);
            text-align: center;
            width: 380px;
            color: #e0e0e0;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #00ffd5;
            text-shadow: 0px 0px 10px rgba(0, 255, 213, 0.7);
            margin-bottom: 20px;
        }

        .input-group {
            margin: 20px 0;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            color: #00ffd5;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #00ffd5;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #00ffd5;
        }

        .btn {
            background-color: #4895ef;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #3a86ff;
            box-shadow: 0px 0px 10px rgba(72, 149, 239, 0.7);
        }

        .redirect {
            margin-top: 20px;
        }

        .redirect a {
            color: #00ffd5;
            text-decoration: none;
            font-weight: bold;
        }

        .redirect a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Login</h2>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form id="loginForm" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="redirect">Don't have an account? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>
