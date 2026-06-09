 
<?php
// Include the connection and header files
include('connection.php');
include('header.php');
include('footer.php');

// You can include any other PHP logic here for the home page content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimePassTickets</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h2>Welcome to TimePassTickets</h2>


    <h3>Admin Pages</h3>
    <ul>
       
        <li><a href="bookedseats_curd.php">Booked Seats</a></li>
        <li><a href="booking_curd.php">Booking Management</a></li>
        <li><a href="moives_curd.php">Manage Movies</a></li>
        <li><a href="payment_curd.php">Manage Payments</a></li>
        <li><a href="screen_curd.php">Manage Screens</a></li>
        <li><a href="seats_curd.php">Manage Seats</a></li>
        <li><a href="seattable_curd.php">Seat Tables</a></li>
        <li><a href="showtime_curd.php">Showtimes</a></li>
        <li><a href="theater_curd.php">Theater </a></li>
        <li><a href="usermanagement_curd.php">Usermanagement</a></li>

    </ul>

</main>

<?php
// Include footer
include('footer.php');
?>
