<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

// Lấy schedule_id từ query string
$schedule_id = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : 0;

if ($schedule_id <= 0) {
    echo json_encode(["error" => "Invalid schedule_id"]);
    exit;
}

// Đường dẫn cơ sở cho ảnh poster
$base_url = "http://localhost/web-project/backend/assets/images/";

// Truy vấn để lấy thông tin lịch chiếu và thông tin phim tương ứng
$query = "
    SELECT s.schedule_id, s.movie_id, m.title AS movie_title, m.poster, m.duration, m.release_date, m.status,
           GROUP_CONCAT(g.genre_name SEPARATOR ', ') AS genres,
           s.show_date, s.show_time, s.theater, s.available_seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE s.schedule_id = ?
    GROUP BY s.schedule_id
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $schedule = $result->fetch_assoc();
    // Thêm base_url vào poster
    $schedule['poster'] = $base_url . $schedule['poster'];
    echo json_encode($schedule);
} else {
    echo json_encode(["error" => "No schedule found for this ID"]);
}
