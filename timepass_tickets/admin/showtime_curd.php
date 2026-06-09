<?php
require 'connection.php'; // Include database connection

// Create (Insert)
if (isset($_POST['create'])) {
    $movie_id = intval($_POST['movie_id']);
    $theater_id = intval($_POST['theater_id']);
    $screen_id = intval($_POST['screen_id']);
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = floatval($_POST['price']);

    $sql = "INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissd", $movie_id, $theater_id, $screen_id, $show_date, $show_time, $price);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Showtime added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Update
if (isset($_POST['update'])) {
    $showtime_id = intval($_POST['showtime_id']);
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = floatval($_POST['price']);

    $sql = "UPDATE showtimes SET show_date=?, show_time=?, price=? WHERE showtime_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $show_date, $show_time, $price, $showtime_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Showtime updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Delete
if (isset($_POST['delete'])) {
    $showtime_id = intval($_POST['showtime_id']);

    $sql = "DELETE FROM showtimes WHERE showtime_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $showtime_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Showtime deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}

// Read (Retrieve)
$result = $conn->query("SELECT * FROM showtimes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
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

        h2 {
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
    </style>
</head>

<body>
    <h2>Showtimes Management</h2>

    <form method="post">
        <input type="text" name="movie_id" placeholder="Movie ID" required>
        <input type="text" name="theater_id" placeholder="Theater ID" required>
        <input type="text" name="screen_id" placeholder="Screen ID" required>
        <input type="date" name="show_date" required>
        <input type="time" name="show_time" required>
        <input type="text" name="price" placeholder="Price" required>
        <button type="submit" name="create">Add Showtime</button>
    </form>

    <h3>Showtimes List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Movie ID</th>
            <th>Theater ID</th>
            <th>Screen ID</th>
            <th>Show Date</th>
            <th>Show Time</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['showtime_id']) ?></td>
                <td><?= htmlspecialchars($row['movie_id']) ?></td>
                <td><?= htmlspecialchars($row['theater_id']) ?></td>
                <td><?= htmlspecialchars($row['screen_id']) ?></td>
                <td><?= htmlspecialchars($row['show_date']) ?></td>
                <td><?= htmlspecialchars($row['show_time']) ?></td>
                <td><?= htmlspecialchars($row['price']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="showtime_id" value="<?= htmlspecialchars($row['showtime_id']) ?>">
                        <input type="date" name="show_date" required>
                        <input type="time" name="show_time" required>
                        <input type="text" name="price" placeholder="New Price" required>
                        <button type="submit" name="update">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="showtime_id" value="<?= htmlspecialchars($row['showtime_id']) ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php $conn->close(); ?>
</body>
</html>
