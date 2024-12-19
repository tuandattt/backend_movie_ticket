<?php
session_start();
include '../../includes/config.php';

// Lấy danh sách các người dùng đã nhắn tin với admin
$userQuery = "
    SELECT DISTINCT u.user_id, u.username, u.name
    FROM messages m
    JOIN users u ON (m.sender_id = u.user_id OR m.receiver_id = u.user_id)
    WHERE u.role = 'user'
";
$result = $conn->query($userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat</title>
    <style>
        #user-list {
            width: 30%;
            float: left;
            border: 1px solid #ccc;
            height: 500px;
            overflow-y: auto;
        }
        #chat-box {
            width: 70%;
            float: left;
            border: 1px solid #ccc;
            height: 500px;
            overflow-y: scroll;
            padding: 10px;
        }
        #message-input {
            width: calc(70% - 10px);
            float: left;
            margin-top: 10px;
        }
        #send-button {
            float: left;
            margin-top: 10px;
        }
        .user-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }
        .user-item:hover {
            background-color: #f0f0f0;
        }
        .message {
            margin: 5px 0;
            padding: 5px;
            border-radius: 5px;
        }
        .message.admin {
            text-align: right;
            color: blue;
            background-color: #e6f7ff;
        }
        .message.user {
            text-align: left;
            color: green;
            background-color: #f6ffed;
        }
    </style>
</head>
<body>
    <h1>Admin Chat</h1>
    <a href="/web-project/backend/views/admin/dashboard.php">Quay lại Trang Chủ Admin</a>

    <!-- Danh sách người dùng -->
    <div id="user-list">
        <h3>Người Dùng</h3>
        <?php while ($user = $result->fetch_assoc()): ?>
            <div class="user-item" data-user-id="<?php echo $user['user_id']; ?>">
                <strong><?php echo htmlspecialchars($user['name'] ?? $user['username']); ?></strong>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Hộp thoại chat -->
    <div id="chat-box"></div>

    <!-- Nhập tin nhắn -->
    <textarea id="message-input" placeholder="Nhập tin nhắn..." rows="3"></textarea>
    <button id="send-button">Gửi</button>

    <!-- Thêm ID admin vào JS -->
    <input type="hidden" id="admin-id" value="<?php echo $_SESSION['user_id']; ?>">

    <script>
        const userList = document.querySelectorAll(".user-item");
        const chatBox = document.getElementById("chat-box");
        const messageInput = document.getElementById("message-input");
        const sendButton = document.getElementById("send-button");
        const adminId = document.getElementById("admin-id").value;

        let currentUserId = null;

        // Xử lý khi chọn người dùng từ danh sách
        userList.forEach(user => {
            user.addEventListener("click", () => {
                currentUserId = user.dataset.userId;
                fetchMessages();
            });
        });

        // Hàm tải tin nhắn
        function fetchMessages() {
            if (!currentUserId) return;

            fetch(`http://localhost/web-project/backend/controllers/fetch_messages.php?receiver_id=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        chatBox.innerHTML = ""; // Xóa nội dung cũ
                        data.messages.forEach(msg => {
                            const messageDiv = document.createElement("div");
                            messageDiv.classList.add("message");
                            messageDiv.classList.add(msg.sender_id == adminId ? "admin" : "user");
                            messageDiv.innerHTML = `<strong>${msg.sender_name}:</strong> ${msg.message_text}`;
                            chatBox.appendChild(messageDiv);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight; // Cuộn xuống cuối hộp chat
                    }
                })
                .catch(error => console.error("Error fetching messages:", error));
        }

        // Hàm gửi tin nhắn
        function sendMessage() {
    // Kiểm tra nếu nội dung tin nhắn trống hoặc không có user được chọn
    const message = messageInput.value.trim();
    if (!message) {
        console.error("Tin nhắn không được để trống.");
        return;
    }

    if (!currentUserId) {
        console.error("Không có người dùng được chọn để gửi tin nhắn.");
        return;
    }

    // Gửi yêu cầu tới server để lưu tin nhắn
    fetch("http://localhost/web-project/backend/controllers/admin_send_message.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `receiver_id=${currentUserId}&message=${encodeURIComponent(message)}`,
        credentials: "include" // Đảm bảo gửi cookie cùng yêu cầu
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok.");
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                // Tin nhắn được gửi thành công
                messageInput.value = ""; // Xóa nội dung trong ô nhập tin nhắn
                console.log("Tin nhắn đã được gửi thành công.");
                fetchMessages(); // Tải lại danh sách tin nhắn
            } else {
                // Xử lý lỗi từ server
                console.error("Error sending message:", data.message);
                alert(data.message);
            }
        })
        .catch(error => {
            // Xử lý lỗi trong quá trình gửi yêu cầu
            console.error("Error sending message:", error);
        });
}


        // Gửi tin nhắn khi nhấn nút gửi
        sendButton.addEventListener("click", sendMessage);

        // Gửi tin nhắn khi nhấn Enter
        messageInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault(); // Ngăn chặn hành động mặc định (dòng mới)
                sendMessage();
            }
        });

        // Tải tin nhắn định kỳ mỗi 2 giây
        setInterval(() => {
            if (currentUserId) fetchMessages();
        }, 2000);
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-top: 20px;
        }

        .container {
            display: flex;
            flex-direction: row;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        #user-list {
            width: 30%;
            background-color: #e9f5ff;
            border-right: 2px solid #007bff;
            overflow-y: auto;
            max-height: 500px;
        }

        #user-list h3 {
            text-align: center;
            color: #007bff;
            padding: 10px 0;
            margin: 0;
            background-color: #f0f8ff;
            border-bottom: 1px solid #007bff;
        }

        .user-item {
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            color: #333;
            font-weight: bold;
        }

        .user-item:hover {
            background-color: #cce5ff;
            color: #007bff;
        }

        #chat-container {
            width: 70%;
            display: flex;
            flex-direction: column;
        }

        #chat-box {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            max-height: 500px;
            border-bottom: 2px solid #007bff;
            background-color: #ffffff;
        }

        #chat-box .message {
            margin: 10px 0;
  padding: 10px;
  border-radius: 8px;
  max-width: 70%;
  word-wrap: break-word;
  display: flex;
  clear: both;
        }

        .message.admin {
    align-self: flex-start;
    background-color: #d1ecf1;
    color: #0c5460;
    text-align: left;
    float: left; /* Căn trái */
}

.message.user {
    align-self: flex-end;
    background-color: #d4edda;
    color: #155724;
    text-align: right;
    float: right; /* Căn phải */
}


        #message-input-container {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f0f8ff;
        }

        #message-input {
            flex-grow: 1;
            border: 2px solid #007bff;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }

        #send-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #send-button:hover {
            background-color: #0056b3;
        }

        a.back-link {
            display: inline-block;
            margin: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a.back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>

</html>