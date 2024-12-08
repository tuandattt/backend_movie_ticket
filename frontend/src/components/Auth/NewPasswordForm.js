import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "sonner";
import "./Auth.css";

function NewPasswordForm() {
  const navigate = useNavigate();
  const [newPassword, setNewPassword] = useState("");

  useEffect(() => {
    document.body.classList.add("auth-background");
    return () => {
      document.body.classList.remove("auth-background");
    };
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();

    // Lấy user_id từ localStorage
    const userId = localStorage.getItem("user_id");
    if (!userId) {
      toast.error("Không thể xác định người dùng!");
      return;
    }

    if (!newPassword) {
      toast.error("Vui lòng nhập mật khẩu mới!");
      return;
    }

    try {
      const response = await fetch(
        "http://localhost/web-project/backend/views/user/update_password.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            user_id: userId,
            new_password: newPassword, // Chú ý sử dụng đúng tên key 'new_password'
          }),
        }
      );

      const data = await response.json();

      if (data.status === "success") {
        toast.success("Mật khẩu đã được cập nhật thành công!");
        localStorage.removeItem("user_id"); // Xóa user_id khỏi localStorage
        setTimeout(() => {
          navigate("/login");
        }, 2000);// Chuyển đến trang đăng nhập
      } else {
        toast.error(data.message || "Đã xảy ra lỗi.");
      }
    } catch (error) {
      console.error("Error updating password:", error);
      toast.error("Không thể cập nhật mật khẩu. Vui lòng thử lại.");
    }
  };

  return (
    <div id="newPasswordForm" className="wrapper">
      <div className="title">NEW PASSWORD</div>
      <form onSubmit={handleSubmit}>
        <div className="field">
          <input
            type="password"
            required
            placeholder=" "
            value={newPassword}
            onChange={(e) => setNewPassword(e.target.value)}
          />
          <label>Nhập mật khẩu mới</label>
        </div>
        <div className="field">
          <input type="submit" value="Update Password" />
        </div>
      </form>
    </div>
  );
}

export default NewPasswordForm;
