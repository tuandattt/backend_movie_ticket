<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lưu tin nhắn
    $receiverId = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if ($message) {
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $userId, $receiverId, $message);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Tin nhắn không được để trống.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy tin nhắn
    $receiverId = intval($_GET['receiver_id']);
    $query = "
        SELECT sender_id, message, timestamp 
        FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $userId, $receiverId, $receiverId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
}
?>
