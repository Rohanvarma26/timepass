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

// Fetch occupied seats for this showtime, movie, and theater
$occupied_seats = [];
$occupied_stmt = $conn->prepare("
    SELECT s.seat_number 
    FROM booked_seats bs
    JOIN bookings b ON bs.booking_id = b.booking_id
    JOIN seats s ON bs.seat_id = s.seat_id
    WHERE b.show_time = ? AND b.movie_id = ? AND b.theater = ?
");
if ($occupied_stmt) {
    $occupied_stmt->bind_param("sis", $showtime, $movie_id, $theater);
    $occupied_stmt->execute();
    $occupied_res = $occupied_stmt->get_result();
    while ($row = $occupied_res->fetch_assoc()) {
        $occupied_seats[] = $row['seat_number'];
    }
    $occupied_stmt->close();
}

// Seating configuration for each theater design
$theater_configs = [
    "PVR Cinemas" => [
        "rows" => ["A", "B", "C", "D", "E", "F"],
        "cols" => 10,
        "grid_columns" => "repeat(11, 1fr)",
        "aisles" => [5], // Aisle after column 5
        "categories" => [
            "A" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "B" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "C" => ["name" => "Premium", "price" => 250.00, "class" => "seat-premium"],
            "D" => ["name" => "Premium", "price" => 250.00, "class" => "seat-premium"],
            "E" => ["name" => "VIP Recliner", "price" => 400.00, "class" => "seat-vip"],
            "F" => ["name" => "VIP Recliner", "price" => 400.00, "class" => "seat-vip"]
        ]
    ],
    "INOX" => [
        "rows" => ["A", "B", "C", "D", "E", "F"],
        "cols" => 8,
        "grid_columns" => "repeat(11, 1fr)", // 8 cols + 2 aisles + 1 row label = 11 tracks
        "aisles" => [2, 6], // Aisle after column 2 and column 6
        "categories" => [
            "A" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "B" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "C" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "D" => ["name" => "Club", "price" => 220.00, "class" => "seat-premium"],
            "E" => ["name" => "Club", "price" => 220.00, "class" => "seat-premium"],
            "F" => ["name" => "Insignia", "price" => 350.00, "class" => "seat-vip"]
        ]
    ],
    "Carnival Cinemas" => [
        "rows" => ["A", "B", "C", "D", "E"],
        "cols" => 8,
        "grid_columns" => "repeat(9, 1fr)", // 8 cols + 1 row label = 9 tracks
        "aisles" => [], // No aisles
        "categories" => [
            "A" => ["name" => "Standard", "price" => 130.00, "class" => "seat-standard"],
            "B" => ["name" => "Standard", "price" => 130.00, "class" => "seat-standard"],
            "C" => ["name" => "Standard", "price" => 130.00, "class" => "seat-standard"],
            "D" => ["name" => "Standard", "price" => 130.00, "class" => "seat-standard"],
            "E" => ["name" => "Gold", "price" => 200.00, "class" => "seat-premium"]
        ]
    ],
    "Cinepolis" => [
        "rows" => ["A", "B", "C", "D", "E", "F", "G"],
        "cols" => 8,
        "grid_columns" => "repeat(10, 1fr)", // 8 cols + 1 aisle + 1 row label = 10 tracks
        "aisles" => [4], // Aisle after column 4
        "categories" => [
            "A" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "B" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "C" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "D" => ["name" => "Standard", "price" => 150.00, "class" => "seat-standard"],
            "E" => ["name" => "VIP", "price" => 280.00, "class" => "seat-vip"],
            "F" => ["name" => "VIP", "price" => 280.00, "class" => "seat-vip"],
            "G" => ["name" => "Macro XE", "price" => 400.00, "class" => "seat-vip"]
        ]
    ]
];

$config = isset($theater_configs[$theater]) ? $theater_configs[$theater] : $theater_configs["PVR Cinemas"];
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
      background-color: #121212;
      font-family: 'Poppins', sans-serif;
      color: white;
    }

    .screen {
      background: linear-gradient(180deg, rgba(30, 144, 255, 0.5) 0%, rgba(30, 144, 255, 0.05) 100%);
      color: white;
      text-align: center;
      padding: 12px;
      margin: 20px auto 40px auto;
      font-weight: bold;
      border-radius: 4px;
      box-shadow: 0 5px 15px rgba(30, 144, 255, 0.3);
      width: 70%;
      letter-spacing: 2px;
      font-size: 0.9rem;
      border-top: 3px solid #1e90ff;
    }

    .seating-container {
      background: rgba(255, 255, 255, 0.03);
      border-radius: 16px;
      padding: 30px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      max-width: 750px;
      margin: 0 auto;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .seating-area {
      display: grid;
      gap: 8px;
      justify-content: center;
      align-items: center;
      margin: 0 auto;
    }

    .row-label {
      font-weight: bold;
      color: #777;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      height: 36px;
      width: 36px;
    }

    .aisle-spacer {
      width: 20px;
      height: 36px;
    }

    .seat {
      width: 36px;
      height: 36px;
      background-color: #1a1a1a;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.2s ease;
      color: #bbb;
      user-select: none;
    }

    /* Standard seat styling */
    .seat-standard {
      border: 1.5px solid #4895ef;
    }
    .seat-standard:hover {
      background-color: rgba(72, 149, 239, 0.25);
      color: white;
      transform: scale(1.05);
    }

    /* Premium seat styling */
    .seat-premium {
      border: 1.5px solid #bf55ec;
      color: #dfa7f2;
    }
    .seat-premium:hover {
      background-color: rgba(191, 85, 236, 0.25);
      color: white;
      transform: scale(1.05);
    }

    /* VIP seat styling */
    .seat-vip {
      border: 1.5px solid #ffcc00;
      color: #ffe680;
    }
    .seat-vip:hover {
      background-color: rgba(255, 204, 0, 0.25);
      color: white;
      transform: scale(1.05);
    }

    /* Selected state */
    .seat.selected {
      background-color: #00ffd5 !important;
      border-color: #00ffd5 !important;
      color: #000 !important;
      box-shadow: 0 0 12px rgba(0, 255, 213, 0.6);
      transform: scale(1.15) !important;
    }

    /* Occupied state */
    .seat.occupied {
      background-color: #2b2b2b !important;
      border-color: #3a3a3a !important;
      color: #555 !important;
      cursor: not-allowed;
      box-shadow: none;
      transform: none !important;
    }

    .legend-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin: 30px auto 10px auto;
      padding: 15px;
      background: rgba(255, 255, 255, 0.02);
      border-radius: 10px;
      max-width: 650px;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8rem;
      color: #ccc;
    }

    .legend-box {
      width: 18px;
      height: 18px;
      border-radius: 4px;
    }

    .legend-box.standard { border: 1.5px solid #4895ef; background: #1a1a1a; }
    .legend-box.premium { border: 1.5px solid #bf55ec; background: #1a1a1a; }
    .legend-box.vip { border: 1.5px solid #ffcc00; background: #1a1a1a; }
    .legend-box.selected { background: #00ffd5; }
    .legend-box.occupied { background: #2b2b2b; border: 1.5px solid #3a3a3a; }

    #selected_text {
      font-weight: bold;
      color: #00ffd5;
    }

    #total_cost_text {
      font-weight: bold;
      color: #ffcc00;
    }

    .btn-danger {
      background: linear-gradient(135deg, #ff006e 0%, #ff4d6d 100%);
      border: none;
      font-weight: bold;
      transition: all 0.3s;
      padding: 14px;
      letter-spacing: 1px;
      font-size: 1rem;
    }

    .btn-danger:hover {
      background: linear-gradient(135deg, #ff4d6d 0%, #ff85a2 100%);
      box-shadow: 0 4px 15px rgba(255, 0, 110, 0.4);
      transform: translateY(-2px);
    }
    
    .btn-danger:disabled {
      background: #2a2a2a !important;
      color: #666 !important;
      transform: none;
      box-shadow: none;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold mb-1">🎭 Select Seats</h2>
    <h5 class="text-secondary"><?= htmlspecialchars($movie['title']) ?> at <span class="text-info"><?= htmlspecialchars($theater) ?></span></h5>
  </div>

  <div class="screen">🎥 SCREEN THIS WAY</div>

  <div class="seating-container">
    <form action="payment.php" method="POST">
      <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
      <input type="hidden" name="theater" value="<?= htmlspecialchars($theater) ?>">
      <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
      <input type="hidden" name="tickets" value="<?= $tickets ?>">
      <input type="hidden" name="selected_seats" id="selected_seats">
      <input type="hidden" name="total_amount" id="total_amount_input" value="0">

      <div class="seating-area my-4" style="grid-template-columns: <?= $config['grid_columns'] ?>;">
        <?php foreach ($config['rows'] as $row_label): 
            $category = $config['categories'][$row_label];
        ?>
            <!-- Row Header Label -->
            <div class="row-label"><?= $row_label ?></div>
            
            <?php for ($col = 1; $col <= $config['cols']; $col++): 
                // Check if we should insert an aisle gap
                if (in_array($col - 1, $config['aisles'])):
            ?>
                <div class="aisle-spacer"></div>
            <?php endif; ?>
            
                <?php 
                    $seat_name = $row_label . $col;
                    $is_occupied = in_array($seat_name, $occupied_seats);
                ?>
                <div class="seat <?= $category['class'] ?> <?= $is_occupied ? 'occupied' : '' ?>" 
                     data-seat="<?= $seat_name ?>" 
                     data-price="<?= $category['price'] ?>" 
                     onclick="selectSeat(this)">
                     <?= $col ?>
                </div>
            <?php endfor; ?>
        <?php endforeach; ?>
      </div>

      <div class="legend-container">
        <div class="legend-item">
          <div class="legend-box standard"></div>
          <span>Standard (₹<?= number_format($config['categories'][$config['rows'][0]]['price']) ?>)</span>
        </div>
        <div class="legend-item">
          <div class="legend-box premium"></div>
          <span>Premium (₹<?= number_format($config['categories'][array_keys($config['categories'])[count($config['rows'])/2]]['price']) ?>)</span>
        </div>
        <div class="legend-item">
          <div class="legend-box vip"></div>
          <span>VIP (₹<?= number_format($config['categories'][$config['rows'][count($config['rows'])-1]]['price']) ?>)</span>
        </div>
        <div class="legend-item">
          <div class="legend-box selected"></div>
          <span>Selected</span>
        </div>
        <div class="legend-item">
          <div class="legend-box occupied"></div>
          <span>Occupied</span>
        </div>
      </div>

      <div class="text-center mt-4">
        <p class="fs-5">
          Tickets Required: <span class="badge bg-secondary"><?= $tickets ?></span> | 
          Selected: <span id="selected_text">None</span>
        </p>
        <h4 class="fw-bold text-warning mb-4">Total Amount: <span id="total_cost_text">₹0.00</span></h4>
      </div>

      <button type="submit" class="btn btn-danger w-100" id="proceedBtn" disabled>
        Proceed to Payment
      </button>
    </form>
  </div>
</div>

<script>
  let selectedSeats = [];
  let totalCost = 0;
  const totalTickets = <?= $tickets ?>;

  function selectSeat(seat) {
    if (seat.classList.contains('occupied')) return;

    const seatNumber = seat.getAttribute('data-seat');
    const seatPrice = parseFloat(seat.getAttribute('data-price'));

    if (seat.classList.contains('selected')) {
      seat.classList.remove('selected');
      selectedSeats = selectedSeats.filter(s => s !== seatNumber);
      totalCost -= seatPrice;
    } else {
      if (selectedSeats.length < totalTickets) {
        seat.classList.add('selected');
        selectedSeats.push(seatNumber);
        totalCost += seatPrice;
      }
    }

    document.getElementById('selected_seats').value = selectedSeats.join(',');
    document.getElementById('selected_text').textContent = selectedSeats.join(', ') || 'None';
    document.getElementById('total_cost_text').textContent = '₹' + totalCost.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('total_amount_input').value = totalCost;
    document.getElementById('proceedBtn').disabled = selectedSeats.length !== totalTickets;
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
