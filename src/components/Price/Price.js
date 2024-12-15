import React from "react";
import { useNavigate } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faRightFromBracket } from "@fortawesome/free-solid-svg-icons";
import "./Price.css"; // CSS file riêng cho bảng giá vé

const TicketPricing = () => {
  const navigate = useNavigate();

  // Hàm điều hướng chung
  const handleNavigate = (path) => {
    navigate(path);
  };

  // Hàm đăng xuất (có thể thêm logic nếu cần)
  const handleLogout = () => {
    console.log("Đăng xuất");
    navigate("/login"); // Chuyển hướng đến trang login
  };

  const pricingData = [
    { category: "Giá gốc", price: "60,000 VND" },
    { category: "Thành viên bạc", price: "Giảm 5%" },
    { category: "Thành viên vàng", price: "Giảm 10%" },
    { category: "Thành viên bạch kim", price: "Giảm 15%" },
    { category: "Thành viên U23", price: "Giảm 18%" },
  ];

  return (
    <>
      <header className="header-container">
        <img
          src="img/cinema_logo2.png"
          alt="Logo"
          className="header-logo"
          onClick={() => navigate("/")} // Điều hướng về trang Home khi click logo
        />
        <nav>
          <ul className="header-menu">
            <li onClick={() => handleNavigate("/showtimes")}>Lịch chiếu</li>
            <li onClick={() => handleNavigate("/account")}>Thông tin tài khoản</li>
            <li className="logout-button" onClick={handleLogout}>
              <FontAwesomeIcon icon={faRightFromBracket} /> Đăng xuất
            </li>
          </ul>
        </nav>
      </header>
      <div className="pricing-container">
        <h1>Bảng Giá Vé</h1>
        <table className="pricing-table">
          <thead>
            <tr>
              <th>Đối tượng</th>
              <th>Giá vé</th>
            </tr>
          </thead>
          <tbody>
            {pricingData.map((item, index) => (
              <tr key={index}>
                <td>{item.category}</td>
                <td>{item.price}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </>
  );
};

export default TicketPricing;
