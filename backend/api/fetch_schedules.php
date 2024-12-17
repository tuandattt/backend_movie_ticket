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
    $schedule_id = $row['schedule_id'];
    $available_seats = $row['available_seats'];

    // Truy vấn để lấy các ghế đã đặt từ bảng bookings
    $check_double_seat_query = "
        SELECT seat_number 
        FROM bookings 
        WHERE schedule_id = ? 
    ";
    $check_double_seat_stmt = $conn->prepare($check_double_seat_query);
    $check_double_seat_stmt->bind_param("i", $schedule_id);
    $check_double_seat_stmt->execute();
    $check_double_seat_stmt->store_result();

    // Khai báo biến để bind dữ liệu
    $check_double_seat_stmt->bind_result($seat_number);

    // Duyệt qua các ghế đã đặt và tính số ghế đã bị đặt
    while ($check_double_seat_stmt->fetch()) {
        // Kiểm tra xem seat_number có phải là ghế đôi (có dấu "-")
        if (strpos($seat_number, '-') !== false) {
            // Nếu là ghế đôi, giảm số ghế trống đi 2
            $available_seats -= 2;
        } else {
            // Nếu là ghế đơn, giảm số ghế trống đi 1
            $available_seats -= 1;
        }
    }

    // Cập nhật lại số ghế trống cho lịch chiếu này
    $row['available_seats'] = $available_seats;

    // Thêm lịch chiếu đã cập nhật vào mảng kết quả
    $schedules[] = $row;
}

// Trả kết quả về dưới dạng JSON
echo json_encode($schedules);
?>
