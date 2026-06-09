<?php
require 'connection.php'; // Include database connection

$message = ""; // To store success/error messages

// Create (Insert User)
if (isset($_POST['create'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = htmlspecialchars($_POST['phone']);

    $stmt = $conn->prepare("INSERT INTO user (name, email, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $phone);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>User added successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Update User
if (isset($_POST['update'])) {
    $user_id = intval($_POST['user_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);

    $stmt = $conn->prepare("UPDATE user SET name=?, email=?, phone=? WHERE user_id=?");
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>User updated successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Delete User
if (isset($_POST['delete'])) {
    $user_id = intval($_POST['user_id']);

    $stmt = $conn->prepare("DELETE FROM user WHERE user_id=?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>User deleted successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Read (Fetch Users)
$result = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
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

        h2 {
            margin-top: 30px;
            color: #ffcc00;
            text-shadow: 1px 1px 3px #000;
        }

        form {
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            width: 90%;
            max-width: 1000px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3);
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
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
    <h2>User Management</h2>

    <!-- Success/Error Messages -->
    <?= $message ?>

    <!-- Create User Form -->
    <form method="post">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <button type="submit" name="create">Add User</button>
    </form>

    <!-- User List -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                        <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
                        <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>
                        <button type="submit" name="update">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php $conn->close(); ?>
</body>
</html>
