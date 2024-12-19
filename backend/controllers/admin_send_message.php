<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");


// Kiểm tra dữ liệu gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $admin_id = 1; // ID của admin cố định
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if (!empty($message)) {
        // Thêm tin nhắn vào cơ sở dữ liệu
        $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $admin_id, $receiver_id, $message);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Tin nhắn đã được gửi bởi admin."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gửi tin nhắn thất bại."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Nội dung tin nhắn không được để trống."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
}
