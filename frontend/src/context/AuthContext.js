import React, { createContext, useState, useEffect } from "react";

// Tạo context
export const AuthContext = createContext();

// Tạo provider
const AuthProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState("");

  // Kiểm tra trạng thái đăng nhập khi ứng dụng khởi động
  useEffect(() => {
    const loggedInStatus = localStorage.getItem("isLoggedIn") === "true";
    const storedUsername = localStorage.getItem("username");
    setIsLoggedIn(loggedInStatus);
    setUsername(storedUsername || "");
  }, []);

  // Hàm xử lý đăng xuất
  const handleLogout = () => {
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("username");
    localStorage.removeItem("token");
    setIsLoggedIn(false);
    setUsername("");
    window.location.href = "/login"; // Chuyển hướng về màn hình đăng nhập
  };

  return (
    <AuthContext.Provider
      value={{ isLoggedIn, username, setIsLoggedIn, setUsername, handleLogout }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export default AuthProvider;
