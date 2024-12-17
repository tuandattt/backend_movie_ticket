<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Kiểm tra xem user_id có trong session không
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

$userId = $_SESSION['user_id'];

// Truy vấn thông tin thẻ thành viên
$query = "
    SELECT user_id, membership_level, DATE(created_at) AS activated_date, total_spent 
    FROM users 
    WHERE user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $membershipInfo = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $membershipInfo]);
} else {
    echo json_encode(["status" => "error", "message" => "Không tìm thấy thông tin thẻ thành viên"]);
}

$stmt->close();
$conn->close();
?>
