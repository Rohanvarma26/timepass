<?php
require 'connection.php'; // Including connection.php to use the database connection

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == "create") {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $duration = $_POST['duration'];
        $release_date = $_POST['release_date'];
        $language = $_POST['language'];
        $poster_url = $_POST['poster_url'];
        
        $sql = "INSERT INTO movies (title, description, genre, duration, release_date, language, poster_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $title, $description, $genre, $duration, $release_date, $language, $poster_url);
        $stmt->execute();
    }
    
    if ($action == "update") {
        $movie_id = $_POST['movie_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $duration = $_POST['duration'];
        $release_date = $_POST['release_date'];
        $language = $_POST['language'];
        $poster_url = $_POST['poster_url'];
        
        $sql = "UPDATE movies SET title=?, description=?, genre=?, duration=?, release_date=?, language=?, poster_url=? WHERE movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $title, $description, $genre, $duration, $release_date, $language, $poster_url, $movie_id);
        $stmt->execute();
    }
    
    if ($action == "delete") {
        $movie_id = $_POST['movie_id'];
        $sql = "DELETE FROM movies WHERE movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT * FROM movies");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movie Management</title>
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

        img {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Movie Management</h2>

        <form method="POST">
            <input type="hidden" name="action" value="create">
            <input type="text" name="title" class="form-control" placeholder="Title" required>
            <input type="text" name="description" class="form-control" placeholder="Description" required>
            <input type="text" name="genre" class="form-control" placeholder="Genre" required>
            <input type="text" name="duration" class="form-control" placeholder="Duration" required>
            <input type="date" name="release_date" class="form-control" required>
            <input type="text" name="language" class="form-control" placeholder="Language" required>
            <input type="text" name="poster_url" class="form-control" placeholder="Poster URL" required>
            <button type="submit" class="btn btn-primary">Add Movie</button>
        </form>

        <h3>Movie List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Genre</th>
                <th>Duration</th>
                <th>Release Date</th>
                <th>Language</th>
                <th>Poster</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['movie_id']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['genre']) ?></td>
                <td><?= htmlspecialchars($row['duration']) ?></td>
                <td><?= htmlspecialchars($row['release_date']) ?></td>
                <td><?= htmlspecialchars($row['language']) ?></td>
                <td><img src="<?= htmlspecialchars($row['poster_url']) ?>" alt="Poster"></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="movie_id" value="<?= htmlspecialchars($row['movie_id']) ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="movie_id" value="<?= htmlspecialchars($row['movie_id']) ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" class="form-control" required>
                        <input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>" class="form-control" required>
                        <input type="text" name="genre" value="<?= htmlspecialchars($row['genre']) ?>" class="form-control" required>
                        <input type="text" name="duration" value="<?= htmlspecialchars($row['duration']) ?>" class="form-control" required>
                        <input type="date" name="release_date" value="<?= htmlspecialchars($row['release_date']) ?>" class="form-control" required>
                        <input type="text" name="language" value="<?= htmlspecialchars($row['language']) ?>" class="form-control" required>
                        <input type="text" name="poster_url" value="<?= htmlspecialchars($row['poster_url']) ?>" class="form-control" required>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
