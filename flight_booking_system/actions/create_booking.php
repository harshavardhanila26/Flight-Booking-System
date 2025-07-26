<?php
// /actions/create_booking.php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['schedule_id']) || !isset($_POST['seat_class']) || !isset($_POST['seat_type'])) {
    header("Location: ../dashboard.php?error=Invalid booking request.");
    exit();
}

$schedule_id = (int)$_POST['schedule_id'];
$user_id = $_SESSION['user_id'];
$seat_class = $_POST['seat_class'];
$seat_type = $_POST['seat_type'];

// Start a transaction to ensure data integrity
$conn->begin_transaction();

try {
    // Lock the flight schedule row to prevent race conditions
    $stmt = $conn->prepare("SELECT available_seats, base_price FROM flight_schedules WHERE schedule_id = ? FOR UPDATE");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Flight schedule not found.");
    }

    $schedule = $result->fetch_assoc();
    $stmt->close();

    // Check if seats are available
    if ($schedule['available_seats'] <= 0) {
        throw new Exception("Sorry, no more seats are available on this flight.");
    }

    // Decrement the available seats count
    $stmt = $conn->prepare("UPDATE flight_schedules SET available_seats = available_seats - 1 WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $stmt->close();

    // Calculate final price based on class and seat type
    $final_price = $schedule['base_price'];
    if ($seat_class === 'Business') {
        $final_price *= 1.8; // 80% markup for Business class
    }
    if ($seat_type === 'Window') {
        $final_price += 1000; // 1000 surcharge for window seat
    }

    // Insert the new booking with 'Pending' status
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, schedule_id, seat_class, seat_type, final_price, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisid", $user_id, $schedule_id, $seat_class, $seat_type, $final_price);
    $stmt->execute();
    $new_booking_id = $stmt->insert_id;
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Redirect to payment page
    header("Location: ../payment.php?booking_id=" . $new_booking_id);
    exit();

} catch (Exception $e) {
    // Roll back the transaction on error
    $conn->rollback();
    // Redirect back to dashboard with an error
    header("Location: ../dashboard.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>