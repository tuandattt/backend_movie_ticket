<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Kiểm tra nếu user_id tồn tại trong session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

$userId = $_SESSION['user_id'];

// Nhận dữ liệu từ frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['age'], $data['phone_number'])) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
    exit();
}

$name = $data['name'];
$age = intval($data['age']);
$phone_number = $data['phone_number'];

// Logic cập nhật is_u23_confirmed
$is_u23_confirmed = ($age <= 23) ? 'yes' : 'no';

// Truy vấn cập nhật thông tin người dùng
$query = "
    UPDATE users 
    SET name = ?, age = ?, phone_number = ?, is_u23_confirmed = ? 
    WHERE user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("sissi", $name, $age, $phone_number, $is_u23_confirmed, $userId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Thông tin đã được cập nhật thành công"]);
} else {
    echo json_encode(["status" => "error", "message" => "Cập nhật thông tin thất bại"]);
}

$stmt->close();
$conn->close();
?>
