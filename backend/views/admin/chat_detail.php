<?php
session_start();
include '../../includes/config.php';


$adminId = $_SESSION['user_id'];
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$userId) {
    echo "Không có ID người dùng để hiển thị chi tiết trò chuyện.";
    exit();
}

// Lấy thông tin người dùng
$userQuery = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    echo "Người dùng không tồn tại.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Chat</title>
    <style>
        #chat-box {
            width: 100%;
            height: 500px;
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
            text-align: left;
            color: green;
            background-color: #f6ffed;
        }
        .message.admin {
            text-align: right;
            color: blue;
            background-color: #e6f7ff;
        }
        #message-input {
            width: calc(100% - 90px);
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Trò Chuyện với <?php echo htmlspecialchars($user['name'] ?? $user['username']); ?> (#<?php echo $user['user_id']; ?>)</h1>
    <a href="admin_chat.php">Quay lại danh sách người dùng</a>
    <div id="chat-box"></div>

    <div>
        <textarea id="message-input" placeholder="Nhập tin nhắn..." rows="3"></textarea>
        <button id="send-button">Gửi</button>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const adminId = <?php echo json_encode($adminId); ?>;
        const userId = <?php echo json_encode($userId); ?>;

        // Fetch messages
        function fetchMessages() {
            fetch(`http://localhost/web-project/backend/controllers/fetch_messages.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        chatBox.innerHTML = '';
                        data.messages.forEach(msg => {
                            const messageDiv = document.createElement('div');
                            messageDiv.classList.add('message');
                            messageDiv.classList.add(msg.sender_id == adminId ? 'admin' : 'user');
                            messageDiv.textContent = msg.message;
                            chatBox.appendChild(messageDiv);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });
        }

        // Send message
        sendButton.addEventListener('click', () => {
            const message = messageInput.value.trim();
            if (message) {
                fetch('http://localhost/web-project/backend/controllers/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `receiver_id=${userId}&sender_id=${adminId}&message=${encodeURIComponent(message)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            messageInput.value = '';
                            fetchMessages();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        });

        // Fetch messages every 2 seconds
        setInterval(fetchMessages, 2000);
        fetchMessages();
    </script>
</body>
</html>