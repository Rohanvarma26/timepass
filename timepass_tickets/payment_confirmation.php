<?php
session_start();
include_once "connection.php";

// Check if required data is available
if (
    !isset($_POST['movie_id']) || empty($_POST['movie_id']) ||
    !isset($_POST['theater']) || empty($_POST['theater']) ||
    !isset($_POST['showtime']) || empty($_POST['showtime']) ||
    !isset($_POST['tickets']) || empty($_POST['tickets']) ||
    !isset($_POST['selected_seats']) || empty($_POST['selected_seats']) ||
    !isset($_POST['total_amount']) || empty($_POST['total_amount']) ||
    !isset($_POST['payment_method']) || empty($_POST['payment_method'])
) {
    die("<h3 style='color: red; text-align: center;'>Invalid payment details!</h3>");
}

$movie_id = intval($_POST['movie_id']);
$theater = htmlspecialchars($_POST['theater']);
$showtime = htmlspecialchars($_POST['showtime']);
$tickets = intval($_POST['tickets']);
$selected_seats = htmlspecialchars($_POST['selected_seats']);
$total_amount = floatval($_POST['total_amount']);
$payment_method = htmlspecialchars($_POST['payment_method']);

// Simulating payment process
$payment_status = "success";
$transaction_id = uniqid("TXN_");
$booked_at = date("Y-m-d H:i:s");

// Redirect after 5 seconds
echo "<script>
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 5000);
</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #141414; color: cyan; font-family: 'Open Sans', sans-serif; }
        .container { max-width: 500px; margin-top: 30px; }
        .card { background: #1e1e1e; padding: 30px; border-radius: 10px; text-align: center; box-shadow: 0px 0px 20px rgba(255, 0, 0, 0.5); color: white; }
        .btn-custom { background-color: #e50914; border: none; padding: 10px 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .btn-custom:hover { background-color: #ff1e22; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2 class="text-danger fw-bold">Payment Successful!</h2>
        <p class="text-secondary">Your booking has been confirmed. You will be redirected to the home page shortly.</p>
        <hr style="border-color: rgba(255,255,255,0.2);">
        <h4 class="fw-bold">Movie: <?= htmlspecialchars($movie_id) ?></h4>
        <p><strong>Theater:</strong> <?= htmlspecialchars($theater) ?></p>
        <p><strong>Showtime:</strong> <?= htmlspecialchars($showtime) ?></p>
        <p><strong>Seats:</strong> <?= htmlspecialchars($selected_seats) ?></p>
        <p><strong>Total Tickets:</strong> <?= $tickets ?></p>
        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($transaction_id) ?></p>
        <h4 class="fw-bold text-success">Amount Paid: ₹<?= $total_amount ?></h4>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment_method) ?></p>
        <p><strong>Payment Status:</strong> <?= htmlspecialchars($payment_status) ?></p>
        <p><strong>Booked At:</strong> <?= htmlspecialchars($booked_at) ?></p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
