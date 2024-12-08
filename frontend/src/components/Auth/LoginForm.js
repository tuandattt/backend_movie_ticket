import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "sonner";
import "./Auth.css";

function LoginForm() {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [rememberMe, setRememberMe] = useState(false);

  useEffect(() => {
    document.body.classList.add("auth-background");
    return () => {
      document.body.classList.remove("auth-background");
    };
  }, []);

  useEffect(() => {
    // Lấy thông tin đăng nhập từ localStorage
    const savedUsername = localStorage.getItem("savedUsername");
    const savedPassword = localStorage.getItem("savedPassword");

    if (savedUsername && savedPassword) {
      setUsername(savedUsername);
      setPassword(savedPassword);
      setRememberMe(true);
    }
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch(
        "http://localhost/web-project/backend/views/user/login.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ username, password }),
        }
      );

      const data = await response.json();

      if (data.status === "success") {
        toast.success("Đăng nhập thành công!");
        localStorage.setItem("isLoggedIn", true);
        localStorage.setItem("username", data.username);
        // Điều hướng đến trang chủ sau khi đăng nhập
        if (rememberMe) {
          localStorage.setItem("savedUsername", username);
          localStorage.setItem("savedPassword", password);
        } else {
          localStorage.removeItem("savedUsername");
          localStorage.removeItem("savedPassword");
        }
        setTimeout(() => {
          navigate("/");
        }, 2000);
      } else {
        toast.error(data.message || "Đăng nhập không thành công!");
      }
    } catch (error) {
      toast.error("Đã xảy ra lỗi. Vui lòng thử lại.");
    }
  };

  return (
    <div id="loginForm" className="wrapper">
      <div className="title">SIGN IN</div>
      <form onSubmit={handleSubmit}>
        <div className="field">
          <input
            type="text"
            required
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            autoComplete="username"
          />
          <label>Tên đăng nhập</label>
        </div>
        <div className="field">
          <input
            type="password"
            required
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            autoComplete="current-password"
          />
          <label>Mật khẩu</label>
        </div>
        <div className="field">
          <input type="submit" value="Login" />
        </div>
        <div className="content">
          <div className="checkbox">
            <input
              type="checkbox"
              id="remember-me"
              checked={rememberMe}
              onChange={(e) => setRememberMe(e.target.checked)}
            />
            <label htmlFor="remember-me">Nhớ tài khoản</label>
          </div>
          <div className="pass-link">
            <a
              href="#"
              role="button"
              onClick={() => navigate("/forgot-password")}
            >
              Quên mật khẩu?
            </a>
          </div>
        </div>
        <div className="signup-link">
          Bạn chưa có tài khoản ?{" "}
          <a href="#" role="button" onClick={() => navigate("/signup")}>
            Đăng ký ngay
          </a>
        </div>
      </form>
    </div>
  );
}

export default LoginForm;
