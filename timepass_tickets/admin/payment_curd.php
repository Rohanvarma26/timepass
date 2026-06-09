<?php
require 'connection.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == "create") {
        $booking_id = $_POST['booking_id'];
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        $payment_status = $_POST['payment_status'];
        $transaction_id = $_POST['transaction_id'];
        
        $sql = "INSERT INTO payments (booking_id, user_id, amount, payment_status, transaction_id, paid_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidss", $booking_id, $user_id, $amount, $payment_status, $transaction_id);
        $stmt->execute();
    }
    
    if ($action == "update") {
        $payment_id = $_POST['payment_id'];
        $booking_id = $_POST['booking_id'];
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        $payment_status = $_POST['payment_status'];
        $transaction_id = $_POST['transaction_id'];
        
        $sql = "UPDATE payments SET booking_id=?, user_id=?, amount=?, payment_status=?, transaction_id=? WHERE payment_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidssi", $booking_id, $user_id, $amount, $payment_status, $transaction_id, $payment_id);
        $stmt->execute();
    }
    
    if ($action == "delete") {
        $payment_id = $_POST['payment_id'];
        $sql = "DELETE FROM payments WHERE payment_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT * FROM payments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://source.unsplash.com/1600x900/?movie-theater') no-repeat center center/cover;
            color: #fff;
            margin: 0;
            padding: 0;
            text-align: center;
            min-height: 100vh;
            backdrop-filter: brightness(0.5);
        }

        h2, h3 {
            margin-top: 30px;
            color: #ffcc00;
            text-shadow: 1px 1px 3px #000;
        }

        form {
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            width: 90%;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3);
        }

        input[type="text"], input[type="number"], select {
            padding: 10px;
            margin: 5px;
            width: 180px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            background: #222;
            color: #fff;
        }

        button {
            background-color: #ffcc00;
            color: #000;
            padding: 10px 16px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e6b800;
        }

        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #ffcc00;
        }

        th {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
            font-weight: bold;
        }

        td {
            background: rgba(255, 255, 255, 0.05);
        }

        form[style*="display:inline-block"] input,
        form[style*="display:inline-block"] select {
            width: auto;
            padding: 6px;
            margin: 2px;
            font-size: 13px;
        }

        p {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Manage Payments</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <input type="text" name="booking_id" placeholder="Booking ID" required>
        <input type="text" name="user_id" placeholder="User ID" required>
        <input type="text" name="amount" placeholder="Amount" required>
        <input type="text" name="transaction_id" placeholder="Transaction ID" required>
        <select name="payment_status" required>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
        </select>
        <button type="submit">Add Payment</button>
    </form>
    
    <h3>Payments List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Booking ID</th>
            <th>User ID</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Transaction ID</th>
            <th>Paid At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['payment_id']) ?></td>
            <td><?= htmlspecialchars($row['booking_id']) ?></td>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['amount']) ?></td>
            <td><?= htmlspecialchars($row['payment_status']) ?></td>
            <td><?= htmlspecialchars($row['transaction_id']) ?></td>
            <td><?= htmlspecialchars($row['paid_at']) ?></td>
            <td>
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($row['payment_id']) ?>">
                    <button type="submit">Delete</button>
                </form>
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($row['payment_id']) ?>">
                    <input type="text" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>" required>
                    <input type="text" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>" required>
                    <input type="text" name="amount" value="<?= htmlspecialchars($row['amount']) ?>" required>
                    <input type="text" name="transaction_id" value="<?= htmlspecialchars($row['transaction_id']) ?>" required>
                    <select name="payment_status" required>
                        <option value="Paid" <?= $row['payment_status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="Pending" <?= $row['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
