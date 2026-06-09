<?php
require 'connection.php'; // Include database connection

// Create (Insert)
if (isset($_POST['create'])) {
    $name = htmlspecialchars($_POST['name']);
    $location = htmlspecialchars($_POST['location']);
    $total_seats = intval($_POST['total_seats']);
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO theaters (name, location, total_seats, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $name, $location, $total_seats, $created_at);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Theater added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Update
if (isset($_POST['update'])) {
    $theater_id = intval($_POST['theater_id']);
    $name = htmlspecialchars($_POST['name']);
    $location = htmlspecialchars($_POST['location']);
    $total_seats = intval($_POST['total_seats']);

    $sql = "UPDATE theaters SET name=?, location=?, total_seats=? WHERE theater_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $location, $total_seats, $theater_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Theater updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Delete
if (isset($_POST['delete'])) {
    $theater_id = intval($_POST['theater_id']);

    $sql = "DELETE FROM theaters WHERE theater_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $theater_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Theater deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Read (Retrieve)
$result = $conn->query("SELECT * FROM theaters");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Theater Management</title>
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

    input[type="text"], input[type="number"] {
        padding: 10px;
        margin: 5px;
        width: 180px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        outline: none;
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

    form[style*="display:inline"] input {
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
    <h2>Theater Management</h2>

    <form method="post">
        <input type="text" name="name" placeholder="Theater Name" required>
        <input type="text" name="location" placeholder="Location" required>
        <input type="number" name="total_seats" placeholder="Total Seats" required>
        <button type="submit" name="create">Add Theater</button>
    </form>

    <h3>Theater List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Total Seats</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['theater_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['total_seats']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="theater_id" value="<?= htmlspecialchars($row['theater_id']) ?>">
                        <input type="text" name="name" placeholder="New Name" required>
                        <input type="text" name="location" placeholder="New Location" required>
                        <input type="number" name="total_seats" placeholder="New Seats" required>
                        <button type="submit" name="update">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="theater_id" value="<?= htmlspecialchars($row['theater_id']) ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php $conn->close(); ?>
</body>
</html>
