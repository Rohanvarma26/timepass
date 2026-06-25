<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Timepass Tickets - Admin Panel</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Bootstrap Stylesheet (Points to parent directory css) -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background-color: #141414; color: white; font-family: 'Open Sans', sans-serif;">

<!-- Navbar Start -->
<div class="container-fluid fixed-top">
    <nav class="navbar navbar-expand-lg" style="background-color: #1c1c1c; padding: 15px;">
        <a href="index.php" class="navbar-brand" style="color: #e50914; font-size: 24px; font-weight: bold;">Movie Booking Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="fa fa-bars" style="color: white;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="index.php" class="nav-link" style="color: white; transition: 0.3s;">Home</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger" style="transition: 0.3s; font-weight: bold;">Logout</a></li>
            </ul>
        </div>
    </nav>
</div>
<!-- Navbar End -->

<!-- Bootstrap JS (Loads from CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
