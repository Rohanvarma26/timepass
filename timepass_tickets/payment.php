<?php
session_start();
include_once "connection.php";

if (!isset($_POST['movie_id']) || empty($_POST['movie_id']) ||
    !isset($_POST['theater']) || empty($_POST['theater']) ||
    !isset($_POST['showtime']) || empty($_POST['showtime']) ||
    !isset($_POST['tickets']) || empty($_POST['tickets']) ||
    !isset($_POST['selected_seats']) || empty($_POST['selected_seats'])
) {
    die("<h3 style='color: red; text-align: center;'>Invalid booking details!</h3>");
}

$movie_id = intval($_POST['movie_id']);
$theater = htmlspecialchars($_POST['theater']);
$showtime = htmlspecialchars($_POST['showtime']);
$tickets = intval($_POST['tickets']);
$selected_seats = is_array($_POST['selected_seats']) ? implode(", ", $_POST['selected_seats']) : htmlspecialchars($_POST['selected_seats']);

include_once "connection.php";
$sql = "SELECT title, price FROM movies WHERE movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if (!$movie) {
    die("Movie not found!");
}

$ticket_price = $movie['price'];
$total_amount = $tickets * $ticket_price;
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Method</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #141414;
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 500px;
            margin-top: 30px;
        }
        .card {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 0px 20px rgba(229, 9, 20, 0.5);
        }
        .btn-custom {
            background-color: #e50914;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            color: white;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #ff1e22;
        }
        select {
            background-color: #141414;
            color: white;
            border: 1px solid #e50914;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2 class="text-danger fw-bold">Select Payment Method</h2>
        <p class="text-secondary">Choose your preferred payment method to complete your booking.</p>
        <hr style="border-color: rgba(255,255,255,0.2);">
        <h4 class="fw-bold">Movie: <?= htmlspecialchars($movie['title']) ?></h4>
        <p><strong>Theater:</strong> <?= htmlspecialchars($theater) ?></p>
        <p><strong>Showtime:</strong> <?= htmlspecialchars($showtime) ?></p>
        <p><strong>Seats:</strong> <?= htmlspecialchars($selected_seats) ?></p>
        <p><strong>Total Tickets:</strong> <?= $tickets ?></p>
        <p><strong>Amount Payable: ₹<?= $total_amount ?></strong></p>
        <form action="payment_confirmation.php" method="POST">
            <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
            <input type="hidden" name="theater" value="<?= htmlspecialchars($theater) ?>">
            <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
            <input type="hidden" name="tickets" value="<?= $tickets ?>">
            <input type="hidden" name="selected_seats" value="<?= htmlspecialchars($selected_seats) ?>">
            <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
            <select name="payment_method" class="form-control mt-3 mb-3" required>
                <option value="Google Pay">Google Pay</option>
                <option value="PhonePe">PhonePe</option>
                <option value="Paytm">Paytm</option>
                <option value="UPI">UPI / QR Code</option>
                <option value="Cash">Cash Payment</option>
            </select>
            <button type="submit" class="btn btn-custom mt-3 w-100">Proceed to Pay</button>
            <a href="index.php" class="btn btn-custom mt-3 w-100">Home</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
