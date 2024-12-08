import "./Header.css";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {faRightFromBracket} from "@fortawesome/free-solid-svg-icons";
const Header = ({ isLoggedIn, username, onLogout }) => {
  return (
    <>
      <div className="top-heading">
        {!isLoggedIn ? (
          <>
            <a href="/login">Đăng nhập</a>
            <span class="divider">|</span>
            <a href="/signup">Đăng ký</a>
          </>
        ) : (
          <div className="user-info">
            <span>Xin chào: {username}</span>
            <button className="logout-button" onClick={onLogout}>
              <FontAwesomeIcon icon = {faRightFromBracket} />
            </button>
          </div>
        )}
      </div>
      <header className="header-container">
        <img src="img\cinema_logo2.png" alt="Logo" className="header-logo" />
        <nav>
          <ul className="header-menu">
            <li>Lịch chiếu</li>
            <li>Giá vé</li>
            <li>Thông tin tài khoản</li>
          </ul>
        </nav>
      </header>
    </>
  );
};

export default Header;
