import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "./Auth.css";
import { toast } from "sonner";

function SignUpForm() {
  const navigate = useNavigate();

  // Các state lưu trữ dữ liệu của form
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const validateEmail = (email) => {
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(email);
  };

  const handleSubmit = async (e) => {
    e.preventDefault(); // Kiểm tra nếu có lỗi trong form (chưa kiểm tra phía client)
    if (!validateEmail(email)) {
      toast.error("Email không hợp lệ! Vui lòng kiểm tra lại.");
      return;
    }
    if (password !== confirmPassword) {
      toast.error("Mật khẩu và xác nhận mật khẩu không khớp.");
      return;
    }

    // Gửi dữ liệu form tới backend
    const response = await fetch(
      "http://localhost/web-project/backend/views/user/register.php",
      {
        method: "POST",
        body: new URLSearchParams({
          username,
          email,
          password,
          confirm_password: confirmPassword,
        }),
      }
    );

    const data = await response.json();

    if (data.status == "success") {
      toast.success(
        "Đăng ký thành công! Bạn sẽ được chuyển đến trang đăng nhập."
      );
      setTimeout(() => {
        navigate("/login"); // Chuyển hướng đến trang đăng nhập sau 2 giây
      }, 2000);
    } else {
      toast.error(
        data.message || "Đăng ký không thành công! Vui lòng thử lại."
      );
    }
  };

  useEffect(() => {
    document.body.classList.add("auth-background");
    return () => {
      document.body.classList.remove("auth-background");
    };
  }, []);

  return (
    <div id="signupForm" className="wrapper">
      <div className="title">SIGN UP</div>
      <form onSubmit={handleSubmit}>
        <div className="field">
          <input
            type="text"
            required
            value={username}
            onChange={(e) => setUsername(e.target.value)}
          />
          <label>Tên đăng nhập</label>
        </div>
        <div className="field">
          <input
            type="text"
            required
            autoComplete="off"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <label>Email</label>
        </div>
        <div className="field">
          <input
            type="password"
            required
            autoComplete="new-password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          <label>Mật khẩu</label>
        </div>
        <div className="field">
          <input
            type="password"
            required
            autoComplete="new-password"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
          />
          <label>Xác nhận lại mật khẩu</label>
        </div>
        <div className="field">
          <input type="submit" value="Register" />
        </div>

        <div className="signin-link">
          Đã có tài khoản?{" "}
          <a href="#" role="button" onClick={() => navigate("/login")}>
            Đăng nhập tại đây
          </a>
        </div>
      </form>
    </div>
  );
}
export default SignUpForm;
