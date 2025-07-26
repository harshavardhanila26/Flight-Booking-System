<?php
// /payment.php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

require_login();

if (!isset($_GET['booking_id'])) {
    header("Location: dashboard.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];

// Updated the query to also fetch seat availability
$stmt = $conn->prepare("
    SELECT 
        b.booking_id, 
        b.final_price, 
        b.seat_type,
        fs.available_seats, -- Fetched available seats
        fr.flight_name, 
        fr.departure_location, 
        fr.arrival_location
    FROM bookings b
    JOIN flight_schedules fs ON b.schedule_id = fs.schedule_id
    JOIN flight_routes fr ON fs.route_id = fr.route_id
    WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'Pending'
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: profile.php?message=Invalid booking or already processed.");
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Payment</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* New styles for the page */
        body {
            background-image: url('assets/images/payment_background.jpg');
            background-color: #f0f8ff; /* A nice fallback color */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 50px;
            vertical-align: middle;
        }
         .logo span {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-left: 10px;
        }
        .nav-links a {
            color: #333; /* Dark text for readability on white navbar */
            text-decoration: none;
            margin-left: 20px;
        }
        /* Style the payment confirmation box */
        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="assets/images/logo.jpg" alt="SkyBooker Logo">
        <span>SkyBooker</span>
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="form-container" style="max-width: 600px; margin: 40px auto;">
        <h2>Confirm Your Booking</h2>
        <p>Please review your booking details and confirm payment.</p>
        
        <h3>Flight: <?php echo htmlspecialchars($booking['flight_name']); ?></h3>
        <p>Route: <?php echo htmlspecialchars($booking['departure_location']); ?> to <?php echo htmlspecialchars($booking['arrival_location']); ?></p>
        
        <p style="font-weight: bold;">Seats Remaining on this flight: <?php echo htmlspecialchars($booking['available_seats']); ?></p>

        <?php
        // This block adds the note ONLY if a window seat was selected
        if ($booking['seat_type'] == 'Window'):
        ?>
            <p style="color: #007bff; font-weight: bold;">Note: The total amount includes the ₹1000 surcharge for your window seat.</p>
        <?php endif; ?>

        <h3>Total Amount: ₹<?php echo number_format($booking['final_price'], 2); ?></h3>

        <form action="actions/confirm_payment_action.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            <input type="hidden" name="amount" value="<?php echo $booking['final_price']; ?>">
            
            <p>This is a simplified payment system. Click below to confirm.</p>
            
            <button type="submit" class="btn">Confirm Payment</button>
        </form>
    </div>
</div>

</body>
</html>