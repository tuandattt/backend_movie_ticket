// Lấy các phần tử DOM
const chatList = document.getElementById('chat-list');
const chatBox = document.getElementById('chat-box');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-button');
let currentUserId = null; // ID người dùng hiện tại trong cuộc trò chuyện

// Tải danh sách người dùng chat
function fetchChatList() {
    fetch('fetch_chat_list.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                chatList.innerHTML = '';
                data.users.forEach(user => {
                    const userDiv = document.createElement('div');
                    userDiv.classList.add('user');
                    userDiv.textContent = `${user.username} (#${user.user_id})`;
                    userDiv.dataset.userId = user.user_id;
                    userDiv.addEventListener('click', () => loadChat(user.user_id));
                    chatList.appendChild(userDiv);
                });
            } else {
                chatList.innerHTML = '<p>Không có người dùng nào để trò chuyện.</p>';
            }
        });
}

// Tải tin nhắn giữa admin và người dùng
function loadChat(userId) {
    currentUserId = userId;
    fetch(`fetch_messages.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                chatBox.innerHTML = '';
                data.messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message');
                    messageDiv.classList.add(msg.sender_id == 5 ? 'admin' : 'user');
                    messageDiv.textContent = msg.message;
                    chatBox.appendChild(messageDiv);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                chatBox.innerHTML = '<p>Không có tin nhắn nào.</p>';
            }
        });
}

// Gửi tin nhắn
sendButton.addEventListener('click', () => {
    const message = messageInput.value.trim();
    if (message && currentUserId) {
        fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `receiver_id=${currentUserId}&message=${encodeURIComponent(message)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageInput.value = '';
                    loadChat(currentUserId);
                } else {
                    alert(data.message);
                }
            });
    }
});

// Cho phép gửi tin nhắn bằng phím Enter
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendButton.click();
    }
});

// Cập nhật tin nhắn theo thời gian thực
setInterval(() => {
    if (currentUserId) {
        loadChat(currentUserId);
    }
}, 2000);

// Tải danh sách chat khi trang được tải
fetchChatList();
