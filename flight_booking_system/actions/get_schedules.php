<?php
// /actions/get_schedules.php
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

if (!isset($_GET['route_id'])) {
    echo json_encode(['error' => 'Route ID not specified.']);
    exit();
}

$route_id = (int)$_GET['route_id'];

// Fetches schedules that are in the future and have seats available
$stmt = $conn->prepare("
    SELECT schedule_id, departure_datetime, base_price, available_seats 
    FROM flight_schedules 
    WHERE route_id = ? AND available_seats > 0 AND departure_datetime > NOW()
    ORDER BY departure_datetime ASC
");
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($schedules);

$stmt->close();
$conn->close();
?>