import "./Header.css";
import React, { useContext } from "react";
import { useNavigate } from "react-router-dom"; // Import useNavigate
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faRightFromBracket } from "@fortawesome/free-solid-svg-icons";
import { AuthContext } from "../../../context/AuthContext";

const Header = () => {
  const navigate = useNavigate(); // Khởi tạo navigate
  const { isLoggedIn, username, handleLogout } = useContext(AuthContext); // Lấy dữ liệu từ context

  const handleLogoClick = () => {
    navigate("/"); // Điều hướng về màn hình chính
  };

  const handleAccountClick = () => {
    if (isLoggedIn) {
      navigate("/account"); // Điều hướng đến màn hình tài khoản
    } else {
      navigate("/login"); // Điều hướng đến màn hình đăng nhập nếu chưa đăng nhập
    }
  };

  const handleTicketClick = () => {
    navigate("/price");
  };

  const handleShowtimesClick = () => {
    navigate("/showtimes");
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
            <li onClick={handleShowtimesClick} style={{ cursor: "pointer" }}>
              Lịch chiếu
            </li>

            <li onClick={handleTicketClick} style={{ cursor: "pointer" }}>
              Giá vé
            </li>
            <li onClick={handleAccountClick} style={{ cursor: "pointer" }}>
              Thông tin tài khoản
            </li>
          </ul>
        </nav>
      </header>
    </>
  );
};

export default Header;
