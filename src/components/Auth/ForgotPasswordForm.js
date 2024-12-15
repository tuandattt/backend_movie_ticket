import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "sonner";
import "./Auth.css";

function ForgotPasswordForm() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");

  useEffect(() => {
    document.body.classList.add("auth-background");
    return () => {
      document.body.classList.remove("auth-background");
    };
  }, []);

  // Trong ForgotPasswordForm.js
const handleSubmit = async (e) => {
  e.preventDefault();
  try {
    const response = await fetch("http://localhost/web-project/backend/views/user/forgot_password.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ email }),
    });

    const data = await response.json();

    if (data.status === "success") {
      localStorage.setItem("user_id", data.user_id); // Lưu user_id vào localStorage
      toast.success("Email hợp lệ! Vui lòng đặt lại mật khẩu.");
      setTimeout(() => {
        navigate("/new-password");
      }, 2000);
    } else {
      toast.error(data.message || "Đã xảy ra lỗi.");
    }
  } catch (error) {
    toast.error("Không thể xác thực email. Vui lòng thử lại.");
  }
};

  return (
    <div id="forgotPasswordForm" className="wrapper">
      <div className="title">FORGOT PASSWORD</div>
      <form onSubmit={handleSubmit}>
        <div className="field">
          <input
            type="text"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <label>Email đăng ký</label>
        </div>
        <div className="field">
          <input type="submit" value="Next" />
        </div>
      </form>
    </div>
  );
}

export default ForgotPasswordForm;
