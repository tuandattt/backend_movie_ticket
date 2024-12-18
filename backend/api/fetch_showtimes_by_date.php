<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Đặt múi giờ về Việt Nam
date_default_timezone_set("Asia/Ho_Chi_Minh");

$currentDate = date("Y-m-d");
$currentTime = date("H:i:s"); // Thời gian hiện tại
$selectedDate = isset($_GET['show_date']) ? $_GET['show_date'] : $currentDate;

// Kiểm tra ngày hợp lệ
if (!$selectedDate || strtotime($selectedDate) < strtotime($currentDate)) {
    echo json_encode(["status" => "error", "message" => "Ngày không hợp lệ"]);
    exit();
}

// Truy vấn để lấy thông tin phim theo ngày, kèm available_seats và title
$query = "
    SELECT DISTINCT m.movie_id, m.title, m.poster, m.duration, 
        GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ', ') AS genres, 
        s.show_time, s.schedule_id, s.available_seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE s.show_date = ? 
    AND (s.show_date > ? OR (s.show_date = ? AND s.show_time > ?)) 
    GROUP BY m.movie_id, m.title, m.poster, m.duration, s.schedule_id, s.show_time, s.available_seats
    ORDER BY m.movie_id, s.show_time ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $selectedDate, $currentDate, $currentDate, $currentTime);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $movieId = $row['movie_id'];

    // Khởi tạo dữ liệu nếu chưa có
    if (!isset($data[$movieId])) {
        $data[$movieId] = [
            "movie_id" => $movieId,
            "title" => $row['title'], // Thêm title
            "poster" => $row['poster'],
            "duration" => $row['duration'],
            "genres" => $row['genres'],
            "show_times" => []
        ];
    }

    // Thêm show_time và available_seats vào danh sách
    $data[$movieId]['show_times'][] = [
        "schedule_id" => $row['schedule_id'],
        "show_time" => $row['show_time'],
        "available_seats" => $row['available_seats']
    ];
}

// Trả về dữ liệu JSON
echo json_encode(["status" => "success", "data" => array_values($data)]);
?>
