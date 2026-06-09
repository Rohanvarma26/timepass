<?php
session_start();
include_once "connection.php"; // Ensure database connection is included

// Function to convert YouTube watch URL to embed URL
function convertWatchToEmbed($watchUrl) {
    preg_match('/v=([a-zA-Z0-9_-]+)/', $watchUrl, $matches);
    if (!empty($matches[1])) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    return $watchUrl; // Return original if not matched
}

// Fetch movies from the database
$sql = "SELECT * FROM movies";
$result = $conn->query($sql);
if (!$result) {
    die("Error fetching movies: " . $conn->error);
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timepass Tickets - Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="styles.css"> <!-- Make sure to place this file in the same directory or adjust the path -->
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a href="index.php" class="navbar-brand fw-bold">🎟 Timepass Tickets</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="about.php" class="nav-link">About Us</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>

                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
                        <li class="nav-item"><a href="register.php" class="nav-link">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Movie Listings -->
    <div class="container py-5 mt-5">
        <h2 class="text-center fw-bold mb-4">🎬 Now Showing</h2>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { 
                $trailerEmbedUrl = convertWatchToEmbed($row['trailer_url']);
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-lg">
                        <img src="<?= htmlspecialchars($row['poster_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($row['description'] ?? 'No description available.'); ?></p>
                            <a href="booking.php?movie_id=<?= $row['movie_id']; ?>" class="btn btn-danger w-100 mb-2">🎟 Book Now</a>
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#trailerModal<?= $row['movie_id']; ?>">🎬 Watch Trailer</button>
                        </div>
                    </div>
                </div>

                <!-- Modal for Trailer -->
                <div class="modal fade" id="trailerModal<?= $row['movie_id']; ?>" tabindex="-1" aria-labelledby="trailerModalLabel<?= $row['movie_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="trailerModalLabel<?= $row['movie_id']; ?>"><?= htmlspecialchars($row['title']); ?> - Trailer</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopTrailer(<?= $row['movie_id']; ?>)"></button>
                            </div>
                            <div class="modal-body">
                                <div class="ratio ratio-16x9">
                                    <iframe id="trailerIframe<?= $row['movie_id']; ?>" src="<?= htmlspecialchars($trailerEmbedUrl); ?>" title="Movie Trailer" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php $conn->close(); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Stop Trailer when Modal Closes -->
    <script>
        function stopTrailer(movieId) {
            var iframe = document.getElementById("trailerIframe" + movieId);
            if (iframe) {
                iframe.src = iframe.src; 
            }
        }
    </script>
</body>
</html>
