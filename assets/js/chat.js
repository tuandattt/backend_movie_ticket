document.addEventListener("DOMContentLoaded", () => {
    const chatBox = document.getElementById("chat-box");
    const messageInput = document.getElementById("message-input");
    const sendButton = document.getElementById("send-button");
    const userId = document.getElementById("user-id").value; // ID người dùng hiện tại
    const adminId = 5; // ID của admin trong hệ thống

    // Hàm tải tin nhắn
    function fetchMessages() {
        fetch(`/movie_booking/controllers/fetch_messages.php?receiver_id=${adminId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    chatBox.innerHTML = ""; // Xóa nội dung cũ
                    data.messages.forEach(msg => {
                        const messageDiv = document.createElement("div");
                        messageDiv.classList.add("message");
                        messageDiv.classList.add(msg.sender_id == userId ? "user" : "admin");
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
        const message = messageInput.value.trim();
        if (message) {
            fetch("/movie_booking/controllers/send_message.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `receiver_id=${adminId}&message=${encodeURIComponent(message)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        messageInput.value = ""; // Xóa nội dung trong ô nhập tin nhắn
                        fetchMessages(); // Tải lại tin nhắn
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error("Error sending message:", error));
        }
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
    setInterval(fetchMessages, 2000);
    fetchMessages(); // Gọi lần đầu tiên khi tải trang
});
