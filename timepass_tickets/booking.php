<?php
session_start();
include_once "connection.php";
include_once "header.php"; // Include the navbar

// Check if movie_id is provided
if (!isset($_GET['movie_id']) || empty($_GET['movie_id'])) {
    die("Movie not found!");
}

$movie_id = intval($_GET['movie_id']);

// Fetch movie details
$sql = "SELECT * FROM movies WHERE movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if (!$movie) {
    die("Movie not found!");
}
?>

<div class="container py-5">
    <h2 class="text-center text-primary fw-bold mb-4">🎟 Book Tickets for <?= htmlspecialchars($movie['title']) ?></h2>

    <div class="card shadow-lg mb-4"> <!-- Styled card -->
        <img src="<?= htmlspecialchars($movie['poster_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']); ?>">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($movie['title']); ?></h4>
            <p class="card-text"><?= htmlspecialchars($movie['description'] ?? 'No description available.'); ?></p>
        </div>
    </div>

    <form action="seat_selection.php" method="GET" class="mt-4">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">

        <div class="mb-3">
            <label class="form-label">Select Theater:</label>
            <select name="theater" class="form-select" required>
                <option value="PVR Cinemas">PVR Cinemas</option>
                <option value="INOX">INOX</option>
                <option value="Carnival Cinemas">Carnival Cinemas</option>
                <option value="Cinepolis">Cinepolis</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Showtime:</label>
            <select name="showtime" class="form-select" required>
                <option value="10:00 AM">10:00 AM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="07:00 PM">07:00 PM</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Number of Tickets:</label>
            <input type="number" name="tickets" class="form-control" min="1" max="10" required>
        </div>

        <button type="submit" class="btn btn-danger w-100">Select Seats</button>
    </form>
</div>

<?php
include_once "footer.php"; // Include the footer
$conn->close();
?>
