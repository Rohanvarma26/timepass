<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Timepass Tickets</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<!-- Navbar Start -->
<div class="container-fluid fixed-top">
    <nav class="navbar navbar-expand-lg">
        <a href="index.php" class="navbar-brand fw-bold">🎟 Timepass Tickets</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="about.php" class="nav-link">About Us</a></li>
                <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
                    <li class="nav-item"><a href="register.php" class="nav-link">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>
<!-- Navbar End -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
