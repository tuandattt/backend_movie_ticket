import React, { useState, useEffect } from "react";
import "./ChatBox.css";

const ChatBox = ({ userId, adminId }) => {
  const [messages, setMessages] = useState([]);
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const [isChatboxOpen, setChatboxOpen] = useState(false); // Quản lý trạng thái mở/đóng

  const fetchMessages = async () => {
    try {
      const response = await fetch(
        `http://localhost/web-project/backend/controllers/fetch_messages.php?receiver_id=1`,
        { credentials: "include" }
      );
      const result = await response.json();

      if (result.status === "success") {
        setMessages(result.messages);
      } else {
        console.error("Error fetching messages:", result.message);
      }
    } catch (error) {
      console.error("Error fetching messages:", error);
    }
  };

  const sendMessage = async () => {
    if (message.trim() === "") {
      console.error("Tin nhắn không được để trống.");
      return;
    }

    setLoading(true);
    try {
      const response = await fetch(
        "http://localhost/web-project/backend/controllers/send_message.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `receiver_id=${adminId}&message=${encodeURIComponent(message)}`,
          credentials: "include",
        }
      );
      const result = await response.json();

      if (response.ok && result.status === "success") {
        setMessage("");
        fetchMessages();
      } else {
        console.error("Error sending message:", result.message);
      }
    } catch (error) {
      console.error("Error sending message:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (isChatboxOpen) {
      fetchMessages();
      const interval = setInterval(fetchMessages, 2000);
      return () => clearInterval(interval);
    }
  }, [isChatboxOpen]);

  return (
    <>
      {/* Nút mở ChatBox */}
      <div className="popup-button" onClick={() => setChatboxOpen(!isChatboxOpen)}>
        💬
      </div>

      {/* ChatBox */}
      <div className={`chatbox-container ${isChatboxOpen ? "active" : ""}`}>
        <div className="chatbox-header">
          <h3>Chat với Admin</h3>
        </div>

        <div className="chatbox-messages">
          {messages.map((msg) => (
            <div
              key={msg.message_id}
              className={`chatbox-message ${
                msg.sender_id === 1 ? "admin-message" : "user-message"
              }`}
            >
              <p>
                <strong>{msg.sender_name}: </strong>
                {msg.message_text}
              </p>
            </div>
          ))}
        </div>

        <div className="chatbox-input">
          <input
            type="text"
            placeholder="Nhập tin nhắn..."
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && sendMessage()}
          />
          <button onClick={sendMessage} disabled={loading}>
            {loading ? "Đang gửi..." : "Gửi"}
          </button>
        </div>
      </div>
    </>
  );
};

export default ChatBox;
