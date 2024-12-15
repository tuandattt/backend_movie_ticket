import "./Header.css";
import React, { useContext } from "react";
import { useNavigate } from "react-router-dom"; // Import useNavigate
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
<<<<<<< HEAD
import {faRightFromBracket} from "@fortawesome/free-solid-svg-icons";
import { useNavigate } from "react-router-dom";
const Header = ({ isLoggedIn, username, onLogout }) => {

  const navigate = useNavigate();

  const handleAccountClick = () => {
    if (!isLoggedIn) {
      navigate("/account"); // Chuyển đến trang thông tin tài khoản
    } else {
      navigate("/login"); // Chuyển đến trang đăng nhập
    }
  };

  const handlePricingClick = () => {
    navigate("/pricing"); // Chuyển đến trang bảng giá vé
=======
import { faRightFromBracket } from "@fortawesome/free-solid-svg-icons";
import { AuthContext } from "../../../context/AuthContext";

const Header = () => {
  const navigate = useNavigate(); // Khởi tạo navigate
  const { isLoggedIn, username, handleLogout } = useContext(AuthContext); // Lấy dữ liệu từ context

  const handleLogoClick = () => {
    navigate("/"); // Điều hướng về màn hình chính
>>>>>>> 408a5811cc1bdb3635d10bc8a85dfb6abb215592
  };

  return (
    <>
      <div className="top-heading">
        {isLoggedIn ? (
          <div className="user-info">
            <span>Xin chào: {username}</span>
            <button className="logout-button" onClick={handleLogout}>
              <FontAwesomeIcon icon={faRightFromBracket} />
            </button>
          </div>
        ) : (
          <>
            <a href="/login">Đăng nhập</a>
            <span className="divider">|</span>
            <a href="/signup">Đăng ký</a>
          </>
        )}
      </div>
      <header className="header-container">
        <img
          src="img/cinema_logo2.png"
          alt="Logo"
          className="header-logo"
          onClick={handleLogoClick} // Thêm sự kiện onClick
          style={{ cursor: "pointer" }} // Đổi con trỏ chuột thành hình bàn tay
        />
        <nav>
          <ul className="header-menu">
            <li>Lịch chiếu</li>
            <li onClick={handlePricingClick} className="pricing-menu">Giá vé</li>
            <li onClick={handleAccountClick} className="account-menu">
              Thông tin tài khoản</li>
          </ul>
        </nav>
      </header>
    </>
  );
};

export default Header;
