<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

$schedule_id = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: http://localhost:3000');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($schedule_id > 0) {
    $query = "SELECT seat_number FROM bookings WHERE schedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookedSeats = [];
    while ($row = $result->fetch_assoc()) {
        $bookedSeats[] = $row['seat_number'];
    }
    echo json_encode($bookedSeats);
} else {
    echo json_encode([]);
}
