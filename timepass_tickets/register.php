<?php
session_start();
include 'connection.php'; // Include database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Prevent SQL injection
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    
    // Check if email already exists
    $check_email = "SELECT * FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $check_email);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already exists. Please use another email.'); window.location.href='register.php';</script>";
        exit();
    }

    // Insert user into database
    $sql = "INSERT INTO user (name, email, password) VALUES ('$name', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration successful! Please login.'); window.location.replace('login.php');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineTime Register</title>
    <link rel="stylesheet" href="styles.css"> <!-- Linking your styles.css file -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .register-container {
            background-color: rgba(0, 0, 0, 0.85); /* Match your .card styling */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 255, 213, 0.5);
            text-align: center;
            width: 380px;
            color: #e0e0e0;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #00ffd5;
            text-shadow: 0px 0px 10px rgba(0, 255, 213, 0.7);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            color: #00ffd5;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input {
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

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #00ffd5;
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            background-color: #4895ef;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .register-btn:hover {
            background-color: #3a86ff;
            box-shadow: 0px 0px 10px rgba(72, 149, 239, 0.7);
            transform: scale(1.05);
        }

        .login-link {
            margin-top: 20px;
        }

        .login-link a {
            color: #00ffd5;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Create Your Account</h1>
        <form id="registerForm" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="Enter your phone number" required>
            </div>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
    <script>
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            let password = document.getElementById("password").value;

            if (password.length < 8) {
                alert("Password must be at least 8 characters long!");
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
