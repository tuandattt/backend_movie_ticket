<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Bạn chưa đăng nhập."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['receiver_id'])) {
    $user_id = $_SESSION['user_id'];
    $receiver_id = intval($_GET['receiver_id']);

    // Lấy danh sách tin nhắn
    $query = "
        SELECT m.message_id, m.sender_id, m.receiver_id, m.message_text, m.timestamp, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.timestamp ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            "message_id" => $row['message_id'],
            "sender_id" => $row['sender_id'],
            "receiver_id" => $row['receiver_id'],
            "message_text" => $row['message_text'],
            "timestamp" => $row['timestamp'],
            "sender_name" => $row['sender_name']
        ];
    }

    echo json_encode(["status" => "success", "messages" => $messages]);
} else {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
}
