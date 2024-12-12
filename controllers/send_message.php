<?php
session_start();
include '../includes/config.php';
// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập.']);
    exit();
}

// Lấy dữ liệu từ request
$senderId = $_SESSION['user_id']; // Người gửi
$receiverId = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null; // Người nhận
$messageText = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$receiverId || empty($messageText)) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
    exit();
}

// Lưu tin nhắn vào cơ sở dữ liệu
$query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $senderId, $receiverId, $messageText);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Tin nhắn đã được gửi.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không thể gửi tin nhắn.']);
}
exit();
?>
