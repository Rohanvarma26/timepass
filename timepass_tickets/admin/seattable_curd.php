<?php
require 'connection.php'; // Including connection.php to use the database connection

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
if (!$result) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            text-align: center;
        }
        h2 {
            color: #ffcc00;
        }
        form {
            background: #333;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }
        input, select, button {
            padding: 10px;
            margin: 5px;
            border: none;
            border-radius: 5px;
        }
        button {
            background: #ffcc00;
            color: #000;
            cursor: pointer;
        }
        button:hover {
            background: #e6b800;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: #222;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ffcc00;
        }
        th {
            background: #444;
        }
        td input {
            margin: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table th, table td {
            text-align: center;
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
