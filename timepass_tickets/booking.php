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

<div class="container py-5 mt-5">
    <h2 class="text-center text-primary fw-bold mb-4">🎟 Book Tickets for <?= htmlspecialchars($movie['title']) ?></h2>

    <div class="card shadow-lg mb-4">
        <div class="row g-0">
            <div class="col-md-4 col-lg-3 text-center p-3">
                <img src="<?= htmlspecialchars($movie['poster_url']); ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($movie['title']); ?>" style="max-height: 380px; object-fit: contain; width: auto; border: none;">
            </div>
            <div class="col-md-8 col-lg-9 d-flex align-items-center">
                <div class="card-body">
                    <h3 class="card-title text-warning fw-bold mb-3"><?= htmlspecialchars($movie['title']); ?></h3>
                    <p class="mb-2"><strong>Genre:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($movie['genre'] ?? 'N/A'); ?></span></p>
                    <p class="mb-2"><strong>Duration:</strong> <span class="text-info"><?= htmlspecialchars($movie['duration'] ?? 'N/A'); ?> mins</span></p>
                    <p class="mb-2"><strong>Language:</strong> <span class="text-white"><?= htmlspecialchars($movie['language'] ?? 'N/A'); ?></span></p>
                    <p class="card-text mt-3 text-secondary"><?= htmlspecialchars($movie['description'] ?? 'No description available.'); ?></p>
                </div>
            </div>
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
