<?php
session_start();
include_once "connection.php";

if (!isset($_GET['movie_id']) || empty($_GET['movie_id']) ||
    !isset($_GET['theater']) || empty($_GET['theater']) ||
    !isset($_GET['showtime']) || empty($_GET['showtime']) ||
    !isset($_GET['tickets']) || empty($_GET['tickets'])) {
    die("Invalid booking details!");
}

$movie_id = intval($_GET['movie_id']);
$theater = htmlspecialchars($_GET['theater']);
$showtime = htmlspecialchars($_GET['showtime']);
$tickets = intval($_GET['tickets']);

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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Seats - <?= htmlspecialchars($movie['title']) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="styles.css">
  
  <style>
    body {
      background-color:rgb(76, 59, 59);
      font-family: 'Poppins', sans-serif;
      color: white;
    }

    .screen {
      background-color:rgb(58, 177, 232);
      color: white;
      text-align: center;
      padding: 10px;
      margin: 30px auto;
      font-weight: bold;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(79, 169, 238, 0.5);
      width: 80%;
    }

    .seating-area {
      display: grid;
      grid-template-columns: repeat(8, 1fr);
      gap: 10px;
      justify-items: center;
      max-width: 500px;
      margin: 0 auto;
    }

    .seat {
      width: 40px;
      height: 40px;
      background-color: #444;
      border: 2px solidrgb(64, 156, 232);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
      color: white;
    }

    .seat.selected {
      background-color: #1e88e5; /* Bright Blue */
      border-color: #1e88e5;
    }

    .seat.occupied {
      background-color: #555;
      cursor: not-allowed;
    }

    #selected_text {
      font-weight: bold;
      color: #1e88e5;
    }

    .btn-danger {
      background-color:rgb(77, 170, 246);
      border: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-danger:hover {
      background-color:rgb(30, 210, 255);
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center text-danger fw-bold mb-4">
    🎭 Select Seats for <?= htmlspecialchars($movie['title']) ?>
  </h2>

  <div class="screen">🎥 SCREEN THIS WAY</div>

  <form action="payment.php" method="POST">
    <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
    <input type="hidden" name="theater" value="<?= $theater ?>">
    <input type="hidden" name="showtime" value="<?= $showtime ?>">
    <input type="hidden" name="tickets" value="<?= $tickets ?>">
    <input type="hidden" name="selected_seats" id="selected_seats">

    <div class="seating-area my-4">
      <?php for ($i = 1; $i <= 40; $i++): ?>
        <div class="seat" data-seat="<?= $i ?>" onclick="selectSeat(this)"><?= $i ?></div>
      <?php endfor; ?>
    </div>

    <p class="text-center">Selected Seats: <span id="selected_text">None</span></p>

    <button type="submit" class="btn btn-danger w-100 mt-3" id="proceedBtn" disabled>
      Proceed to Payment
    </button>
  </form>
</div>

<script>
  let selectedSeats = [];
  const totalTickets = <?= $tickets ?>;

  function selectSeat(seat) {
    if (seat.classList.contains('occupied')) return;

    const seatNumber = seat.getAttribute('data-seat');
    if (seat.classList.contains('selected')) {
      seat.classList.remove('selected');
      selectedSeats = selectedSeats.filter(s => s !== seatNumber);
    } else {
      if (selectedSeats.length < totalTickets) {
        seat.classList.add('selected');
        selectedSeats.push(seatNumber);
      }
    }

    document.getElementById('selected_seats').value = selectedSeats.join(',');
    document.getElementById('selected_text').textContent = selectedSeats.join(', ') || 'None';
    document.getElementById('proceedBtn').disabled = selectedSeats.length !== totalTickets;
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
