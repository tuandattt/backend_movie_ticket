import "./Footer.css";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faFacebookF,
  faTwitter,
  faInstagram,
  faYoutube,
} from "@fortawesome/free-brands-svg-icons";
import { faPhone, faEnvelope } from "@fortawesome/free-solid-svg-icons";
import { useNavigate } from "react-router-dom";
import { useContext } from "react";
import { AuthContext } from "../../../context/AuthContext"; // Import AuthContext

const Footer = () => {
  const navigate = useNavigate();
  const { isLoggedIn } = useContext(AuthContext); // Lấy trạng thái đăng nhập

  const handleAccountClick = () => {
    if (isLoggedIn) {
      navigate("/account"); // Nếu đã đăng nhập, điều hướng tới trang tài khoản
    } else {
      navigate("/login"); // Nếu chưa đăng nhập, điều hướng tới trang đăng nhập
    }
  };

  return (
    <footer className="footer-container">
      <div className="footer-content">
        {/* Logo và slogan */}
        <div className="footer-logo">
          <h2>CINEMA</h2>
          <p>Trải nghiệm phim ảnh tuyệt vời!</p>
        </div>

        {/* Đường dẫn nhanh */}
        <div className="footer-links">
          <h3>Đường Dẫn Nhanh</h3>
          <ul>
            <li>
              <a href="/">Trang Chủ</a>
            </li>
            <li>
              <a href="/showtimes">Lịch Chiếu</a>
            </li>
            <li onClick={handleAccountClick} style={{ cursor: "pointer" }}>
              Tài Khoản
            </li>
            <li>
              <a href="/price">Giá vé</a>
            </li>
          </ul>
        </div>

        {/* Thông tin liên hệ */}
        <div className="footer-contact">
          <h3>Liên Hệ</h3>
          <p>
            <FontAwesomeIcon icon={faPhone} /> Hotline: 1900 1234
          </p>
          <p>
            <FontAwesomeIcon icon={faEnvelope} /> Email: support@movieticket.com
          </p>
        </div>

        {/* Mạng xã hội */}
        <div className="footer-social">
          <h3>Theo Dõi Chúng Tôi</h3>
          <div className="social-icons">
            <a href="#">
              <FontAwesomeIcon icon={faFacebookF} />
            </a>
            <a href="#">
              <FontAwesomeIcon icon={faTwitter} />
            </a>
            <a href="#">
              <FontAwesomeIcon icon={faInstagram} />
            </a>
            <a href="#">
              <FontAwesomeIcon icon={faYoutube} />
            </a>
          </div>
        </div>
      </div>

      {/* Dòng bản quyền */}
      <div className="footer-bottom">
        <p>© 2024 Movie Ticket. All rights reserved.</p>
      </div>
    </footer>
  );
};

export default Footer;
