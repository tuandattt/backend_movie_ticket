<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Bạn chưa đăng nhập."]);
    exit();
}

// Kiểm tra dữ liệu gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if (!empty($message)) {
        // Thêm tin nhắn vào cơ sở dữ liệu
        $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Tin nhắn đã được gửi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gửi tin nhắn thất bại."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Nội dung tin nhắn không được để trống."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
}
