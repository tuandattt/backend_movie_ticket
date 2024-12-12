<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /movie_booking/views/user/login.php");
    exit();
}

$userId = $_SESSION['user_id']; // ID người dùng hiện tại
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat với Admin</title>
    <style>
        #chat-box {
            width: 100%;
            height: 400px;
            border: 1px solid #ccc;
            overflow-y: scroll;
            padding: 10px;
            margin-bottom: 10px;
        }
        .message {
            margin: 5px 0;
            padding: 5px;
            border-radius: 5px;
        }
        .message.user {
            text-align: right;
            color: blue;
            background-color: #e6f7ff;
        }
        .message.admin {
            text-align: left;
            color: green;
            background-color: #f6ffed;
        }
    </style>
</head>
<body>
    <h1>Chat với Admin</h1>
    <a href="/movie_booking/views/user/home.php">Quay lại Trang Chủ</a>
    <div id="chat-box"></div>
    <textarea id="message-input" placeholder="Nhập tin nhắn..." rows="3"></textarea><br>
    <button id="send-button">Gửi</button>

    <!-- Thêm ID người dùng hiện tại để sử dụng trong JavaScript -->
    <input type="hidden" id="user-id" value="<?php echo $userId; ?>">

    <!-- Đường dẫn tới file chat.js -->
    <script src="/movie_booking/assets/js/chat.js"></script>
</body>
</html>
