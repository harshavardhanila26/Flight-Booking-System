<?php
// /actions/confirm_payment_action.php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

require_login();

// Check if the form was submitted correctly
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id'])) {
    header("Location: ../dashboard.php?error=Invalid payment request.");
    exit();
}

$booking_id = (int)$_POST['booking_id'];
$user_id = $_SESSION['user_id'];

// Use a transaction to ensure both updates happen or neither do
$conn->begin_transaction();

try {
    // First, verify the booking exists, is pending, and belongs to the current user
    $stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Booking not found or already processed.");
    }
    $stmt->close();

    // 1. Update the booking status in the `bookings` table
    $update_stmt = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?");
    $update_stmt->bind_param("i", $booking_id);
    $update_stmt->execute();
    $update_stmt->close();

    // 2. Insert the transaction record into the `payments` table
    //    This query no longer tries to insert an 'amount'
    $transaction_id = 'TXN-' . strtoupper(uniqid());
    $payment_status = 'Completed';

    $insert_stmt = $conn->prepare("INSERT INTO payments (booking_id, transaction_id, status) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $booking_id, $transaction_id, $payment_status);
    $insert_stmt->execute();
    $insert_stmt->close();

    // If everything succeeded, commit the changes
    $conn->commit();

    // Redirect to the profile page with a success message
    header("Location: ../profile.php?message=Payment successful! Your booking is confirmed.");
    exit();

} catch (Exception $e) {
    // If anything failed, roll back all database changes
    $conn->rollback();
    header("Location: ../payment.php?booking_id=$booking_id&error=" . urlencode($e->getMessage()));
    exit();
}
?>