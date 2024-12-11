<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");
// Lấy movie_id từ query string
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

$query = "
    SELECT s.schedule_id, m.title AS movie_title, s.show_date, s.show_time, s.theater, s.available_seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE s.movie_id = ?
    ORDER BY s.show_date ASC, s.show_time ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);
