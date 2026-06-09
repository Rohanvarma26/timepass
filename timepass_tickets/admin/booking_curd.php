<?php
include 'connection.php'; // Include the database connection

// Create (Insert Booking)
if (isset($_POST['create'])) {
    $user_id = $_POST['user_id'];
    $show_time = $_POST['show_time'];
    $total_amount = $_POST['total_amount'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, show_time, total_amount, payment_status, booked_at) 
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $user_id, $show_time, $total_amount, $payment_status);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Read (Retrieve Bookings)
$result = $conn->query("SELECT * FROM bookings");

// Update Booking
if (isset($_POST['update'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_POST['user_id'];
    $show_time = $_POST['show_time'];
    $total_amount = $_POST['total_amount'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("UPDATE bookings SET 
                            user_id=?, show_time=?, total_amount=?, payment_status=? 
                            WHERE booking_id=?");
    $stmt->bind_param("isssi", $user_id, $show_time, $total_amount, $payment_status, $booking_id);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Delete Booking
if (isset($_POST['delete'])) {
    $booking_id = $_POST['booking_id'];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id=?");
    $stmt->bind_param("i", $booking_id);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bookings Management</title>
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

        input.form-control, select.form-control {
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
        <h2>Manage Bookings</h2>

        <form method="post">
            <input type="number" name="user_id" class="form-control" placeholder="User ID" required>
            <input type="datetime-local" name="show_time" class="form-control" required>
            <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="Total Amount" required>
            <select name="payment_status" class="form-control" required>
                <option value="Paid">Paid</option>
                <option value="Pending">Pending</option>
            </select>
            <button type="submit" name="create" class="btn btn-primary">Add Booking</button>
        </form>

        <h3>Bookings List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Show Time</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Booked At</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['booking_id']) ?></td>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= htmlspecialchars($row['show_time']) ?></td>
                    <td>₹<?= htmlspecialchars($row['total_amount']) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['booked_at']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
                            <input type="number" name="user_id" class="form-control" placeholder="New User ID" required>
                            <input type="datetime-local" name="show_time" class="form-control" required>
                            <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="New Total Amount" required>
                            <select name="payment_status" class="form-control" required>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                            <button type="submit" name="update" class="btn btn-warning btn-sm">Update</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
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
