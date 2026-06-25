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

// Fetch movie details to display title
$movie_title = "Unknown Movie";
$movie_stmt = $conn->prepare("SELECT title FROM movies WHERE movie_id = ?");
if ($movie_stmt) {
    $movie_stmt->bind_param("i", $movie_id);
    $movie_stmt->execute();
    $movie_stmt->bind_result($movie_title);
    $movie_stmt->fetch();
    $movie_stmt->close();
}

// Simulating payment process
$payment_status = "Paid";
$transaction_id = uniqid("TXN_");
$booked_at = date("Y-m-d H:i:s");

// Resolve user_id from session or fallback to first user in database
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($user_id === 0) {
    $user_res = $conn->query("SELECT user_id FROM user LIMIT 1");
    if ($user_res && $user_res->num_rows > 0) {
        $user_row = $user_res->fetch_assoc();
        $user_id = intval($user_row['user_id']);
    } else {
        // Create a default fallback user
        $conn->query("INSERT INTO user (name, email, password, phone, created_at) VALUES ('Guest User', 'guest@example.com', '12345678', '9999999999', NOW())");
        $user_id = $conn->insert_id;
    }
}

// Start transaction to keep DB updates atomic
$conn->begin_transaction();

try {
    // 1. Insert into bookings table with movie_id and theater name
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, show_time, theater, total_amount, payment_status, booked_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error preparing booking insert: " . $conn->error);
    }
    $stmt->bind_param("iissdss", $user_id, $movie_id, $showtime, $theater, $total_amount, $payment_status, $booked_at);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    // 2. Insert into payments table
    $stmt = $conn->prepare("INSERT INTO payments (booking_id, user_id, amount, payment_status, transaction_id, paid_at) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error preparing payment insert: " . $conn->error);
    }
    $stmt->bind_param("iidsss", $booking_id, $user_id, $total_amount, $payment_status, $transaction_id, $booked_at);
    $stmt->execute();
    $stmt->close();

    // 3. Resolve screen_id from showtimes based on movie and theater
    $screen_id = 1; // Fallback screen
    $theater_like = "%" . explode(" ", $theater)[0] . "%";
    $showtime_res = $conn->prepare("
        SELECT s.screen_id 
        FROM showtimes s
        JOIN theaters t ON s.theater_id = t.theater_id
        WHERE s.movie_id = ? AND t.name LIKE ?
        LIMIT 1
    ");
    if ($showtime_res) {
        $showtime_res->bind_param("is", $movie_id, $theater_like);
        $showtime_res->execute();
        $showtime_res_result = $showtime_res->get_result();
        if ($showtime_res_result && $showtime_res_result->num_rows > 0) {
            $showtime_row = $showtime_res_result->fetch_assoc();
            $screen_id = intval($showtime_row['screen_id']);
        }
        $showtime_res->close();
    }

    // Ultimate fallback to just movie screen if theater match fails
    if ($screen_id === 1) {
        $showtime_res = $conn->query("SELECT screen_id FROM showtimes WHERE movie_id = $movie_id LIMIT 1");
        if ($showtime_res && $showtime_res->num_rows > 0) {
            $showtime_row = $showtime_res->fetch_assoc();
            $screen_id = intval($showtime_row['screen_id']);
        }
    }

    // 4. Split and process seat numbers
    $seat_numbers = explode(",", $selected_seats);
    foreach ($seat_numbers as $seat_num) {
        $seat_num = trim($seat_num);
        if (empty($seat_num)) continue;

        // Check if seat already exists in the seats table
        $seat_check = $conn->prepare("SELECT seat_id FROM seats WHERE screen_id = ? AND seat_number = ?");
        $seat_check->bind_param("is", $screen_id, $seat_num);
        $seat_check->execute();
        $seat_check_res = $seat_check->get_result();

        if ($seat_check_res && $seat_check_res->num_rows > 0) {
            $seat_row = $seat_check_res->fetch_assoc();
            $seat_id = intval($seat_row['seat_id']);
        } else {
            // Insert new seat mapping
            $seat_insert = $conn->prepare("INSERT INTO seats (screen_id, seat_number, seat_type, price) VALUES (?, ?, 'Regular', 150.00)");
            $seat_insert->bind_param("is", $screen_id, $seat_num);
            $seat_insert->execute();
            $seat_id = $conn->insert_id;
            $seat_insert->close();
        }
        $seat_check->close();

        // Link booking with the seat in booked_seats table
        $bs_insert = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_id) VALUES (?, ?)");
        $bs_insert->bind_param("ii", $booking_id, $seat_id);
        $bs_insert->execute();
        $bs_insert->close();
    }

    // Commit transaction if all inserts succeed
    $conn->commit();
} catch (Exception $e) {
    // Rollback transaction on failure
    $conn->rollback();
    die("<h3 style='color: red; text-align: center;'>Failed to save booking: " . htmlspecialchars($e->getMessage()) . "</h3>");
}

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
        <h4 class="fw-bold">Movie: <?= htmlspecialchars($movie_title) ?></h4>
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
