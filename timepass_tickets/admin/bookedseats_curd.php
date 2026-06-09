<?php
include 'connection.php'; // Include database connection

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$error_message = "";

// Create (Insert a Booked Seat)
if (isset($_POST['create'])) {
    $booking_id = intval($_POST['booking_id']);
    $seat_id = intval($_POST['seat_id']);

    $stmt = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $booking_id, $seat_id);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Read (Retrieve booked seats)
try {
    $result = $conn->query("SELECT bs.booking_seat_id, bs.booking_id, bs.seat_id, 
                                   u.name AS user_name, s.seat_number, b.show_time, 
                                   b.total_amount, b.payment_status, b.booked_at
                            FROM booked_seats bs
                            JOIN bookings b ON bs.booking_id = b.booking_id
                            JOIN user u ON b.user_id = u.user_id
                            JOIN seats s ON bs.seat_id = s.seat_id");
} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Update Booked Seat
if (isset($_POST['update'])) {
    $booking_seat_id = intval($_POST['booking_seat_id']);
    $booking_id = intval($_POST['booking_id']);
    $seat_id = intval($_POST['seat_id']);

    $stmt = $conn->prepare("UPDATE booked_seats SET booking_id=?, seat_id=? WHERE booking_seat_id=?");
    $stmt->bind_param("iii", $booking_id, $seat_id, $booking_seat_id);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Delete Booked Seat
if (isset($_POST['delete'])) {
    $booking_seat_id = intval($_POST['booking_seat_id']);

    $stmt = $conn->prepare("DELETE FROM booked_seats WHERE booking_seat_id=?");
    $stmt->bind_param("i", $booking_seat_id);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booked Seats Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://source.unsplash.com/1600x900/?cinema,lights') no-repeat center center/cover;
            color: #fff;
            margin: 0;
            padding: 0;
            text-align: center;
            min-height: 100vh;
            backdrop-filter: brightness(0.5);
        }

        h2, h3 {
            color: #ffcc00;
            text-shadow: 1px 1px 3px #000;
            margin-top: 30px;
        }

        form {
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3);
        }

        input.form-control {
            background-color: #fff;
            border-radius: 5px;
        }

        table {
            width: 100%;
            margin-top: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #ffcc00;
            color: #fff;
        }

        th {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
        }

        td {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-primary {
            background-color: #ffcc00;
            border: none;
            color: #000;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #e6b800;
        }

        .btn-warning {
            background-color: #f0ad4e;
            border: none;
        }

        .btn-danger {
            background-color: #d9534f;
            border: none;
        }

        .alert {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Booked Seats</h2>

        <?php if (!empty($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>

        <form method="post">
            <input type="number" name="booking_id" class="form-control" placeholder="Booking ID" required>
            <input type="number" name="seat_id" class="form-control" placeholder="Seat ID" required>
            <button type="submit" name="create" class="btn btn-primary">Add Booked Seat</button>
        </form>

        <h3>Booked Seats List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Seat</th>
                <th>Show Time</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Booked At</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['booking_seat_id']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['seat_number']) ?></td>
                    <td><?= htmlspecialchars($row['show_time']) ?></td>
                    <td>₹<?= htmlspecialchars($row['total_amount']) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['booked_at']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="booking_seat_id" value="<?= htmlspecialchars($row['booking_seat_id']) ?>">
                            <input type="number" name="booking_id" class="form-control" placeholder="New Booking ID" required>
                            <input type="number" name="seat_id" class="form-control" placeholder="New Seat ID" required>
                            <button type="submit" name="update" class="btn btn-warning btn-sm">Update</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="booking_seat_id" value="<?= htmlspecialchars($row['booking_seat_id']) ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php $conn->close(); ?>
</body>
</html>
