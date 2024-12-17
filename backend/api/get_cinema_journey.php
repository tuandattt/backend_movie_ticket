<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Kiểm tra nếu user_id tồn tại trong session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

$userId = $_SESSION['user_id'];

$query = "
    SELECT 
        p.payment_id AS order_id,
        m.title AS movie_title,
        s.theater AS theater,
        s.show_time AS showtime,
        GROUP_CONCAT(b.seat_number SEPARATOR ', ') AS booked_seats,
        DATE(p.created_at) AS booking_date
    FROM payments p
    JOIN bookings b ON p.payment_id = b.payment_id
    JOIN schedules s ON b.schedule_id = s.schedule_id
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE p.user_id = ?
    GROUP BY p.payment_id, m.title, s.theater, s.show_time, p.created_at
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(["status" => "success", "data" => $data]);
?>
