<?php
// /profile.php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

require_login(); // Protect this page

$user_id = $_SESSION['user_id'];

// Updated query for new database structure
$sql = "
    SELECT
        b.booking_id,
        r.flight_name,
        r.departure_location,
        r.arrival_location,
        s.departure_datetime,
        s.total_seats,
        s.available_seats,
        b.seat_class,
        b.seat_type,
        b.final_price,
        b.status AS booking_status,
        p.payment_date,
        p.transaction_id,
        p.status AS payment_status
    FROM bookings b
    JOIN flight_schedules s ON b.schedule_id = s.schedule_id
    JOIN flight_routes r ON s.route_id = r.route_id
    LEFT JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.user_id = ?
    ORDER BY b.booking_time DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Airline Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-image: url('assets/images/profile_background.jpg');
            background-color: #e0f2ff; /* A fallback color */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo img {
            height: 50px;
            vertical-align: middle;
        }
        .logo span {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .nav-links span {
            color: #555555 !important; /* Force gray for "Welcome" text */
            margin-right: 15px;
        }
        .nav-links a {
            color: #000000 !important; /* Force black for links */
            text-decoration: none;
            margin-left: 15px;
        }
        .nav-links a:hover {
            text-decoration: underline;
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
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>My Booking History</h2>
    <?php if (isset($_GET['message'])) { echo '<p class="success-msg">' . htmlspecialchars($_GET['message']) . '</p>'; } ?>
    <div class="card">
        <?php if ($history_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Flight</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Class</th>
                        <th>Price</th>
                        <th>Seats Available</th>
                        <th>Booking Status</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['flight_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['departure_location']) . ' to ' . htmlspecialchars($row['arrival_location']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['departure_datetime'])); ?></td>
                        <td><?php echo htmlspecialchars($row['seat_class']) . ' (' . htmlspecialchars($row['seat_type']) . ')'; ?></td>
                        <td>₹<?php echo number_format($row['final_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['available_seats']) . ' / ' . htmlspecialchars($row['total_seats']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_status']); ?></td>
                        <td>
                            <?php
                                if ($row['payment_status']) {
                                    echo htmlspecialchars($row['payment_status']);
                                } elseif ($row['booking_status'] == 'Pending') {
                                    echo '<a href="payment.php?booking_id='.$row['booking_id'].'">Pay Now</a>';
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not made any bookings yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>