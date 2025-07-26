<?php
// /actions/get_schedules_action.php
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

if (!isset($_GET['route_id'])) {
    echo json_encode(['error' => 'Route ID not provided.']);
    exit();
}

$route_id = (int)$_GET['route_id'];

$stmt = $conn->prepare("SELECT schedule_id, departure_datetime, base_price FROM flight_schedules WHERE route_id = ? ORDER BY departure_datetime ASC");
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($schedules);