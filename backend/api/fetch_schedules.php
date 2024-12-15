<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

// Lấy movie_id từ query string
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Lấy thời gian hiện tại
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Truy vấn chỉ lấy lịch chiếu chưa qua
$query = "
    SELECT s.schedule_id, m.title AS movie_title, s.show_date, s.show_time, s.theater, s.available_seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE s.movie_id = ? 
    AND (s.show_date > ? OR (s.show_date = ? AND s.show_time >= ?))
    ORDER BY s.show_date ASC, s.show_time ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("isss", $movie_id, $currentDate, $currentDate, $currentTime);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);
?>
