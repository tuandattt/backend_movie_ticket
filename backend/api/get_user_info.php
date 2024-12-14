<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000"); // Địa chỉ frontend
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // Cho phép gửi cookie
header("Content-Type: application/json");

// Kiểm tra nếu user_id tồn tại trong session
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    // Nếu không có user_id trong session, trả về lỗi
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

// Truy vấn thông tin người dùng từ bảng users
$query = "SELECT name, email, phone_number, membership_level, is_u23_confirmed FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userInfo = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $userInfo]);
} else {
    echo json_encode(["status" => "error", "message" => "Không tìm thấy thông tin người dùng"]);
}
?>
