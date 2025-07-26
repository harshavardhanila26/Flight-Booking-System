<?php
// /actions/book_flight_action.php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = (int)$_POST['schedule_id'];
    $seat_class = $_POST['seat_class'];
    $seat_type = $_POST['seat_type'];
    $user_id = $_SESSION['user_id'];

    // --- Get schedule details (price, total seats) ---
    $stmt = $conn->prepare("SELECT base_price, total_seats FROM flight_schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $schedule_result = $stmt->get_result();
    if ($schedule_result->num_rows === 0) {
        header("Location: ../dashboard.php?message=Error: Invalid flight schedule.");
        exit();
    }
    $schedule = $schedule_result->fetch_assoc();
    $base_price = $schedule['base_price'];
    $total_seats = $schedule['total_seats'];
    $stmt->close();

    // --- Check if seats are available ---
    $stmt = $conn->prepare("SELECT COUNT(*) as booked_seats FROM bookings WHERE schedule_id = ? AND status = 'Confirmed'");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $booked_seats = $stmt->get_result()->fetch_assoc()['booked_seats'];
    $stmt->close();

    if ($booked_seats >= $total_seats) {
        header("Location: ../dashboard.php?message=Sorry, this flight is fully booked.");
        exit();
    }

    // --- Calculate Dynamic Price ---
    $class_multiplier = 1.0; // 3rd Class
    if ($seat_class == '2nd Class') $class_multiplier = 1.5;
    if ($seat_class == '1st Class') $class_multiplier = 2.5;
    
    $final_price = $base_price * $class_multiplier;

    // --- ADD THIS BLOCK to add the window seat surcharge ---
    if ($seat_type == 'Window') {
        $final_price += 1000;
    }
    // --- END OF NEW BLOCK ---

    // --- Insert the booking with 'Pending' status ---
    $sql = "INSERT INTO bookings (user_id, schedule_id, seat_class, seat_type, final_price, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $user_id, $schedule_id, $seat_class, $seat_type, $final_price);
    
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        header("Location: ../payment.php?booking_id=" . $booking_id);
    } else {
        header("Location: ../dashboard.php?message=Booking failed. Please try again.");
    }
    
    $stmt->close();
    $conn->close();
}
?>