import React from "react";
import { useNavigate } from "react-router-dom"; // Import useNavigate
import './account.css';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faRightFromBracket } from "@fortawesome/free-solid-svg-icons";

const Account = () => {
  const navigate = useNavigate(); // Khởi tạo useNavigate

  // Giả sử đây là thông tin người dùng từ backend hoặc localStorage
  const userInfo = {
    avatar: "https://example.com/avatar.jpg", // Link ảnh đại diện
    fullName: "Nguyễn Văn A",
    age: 25,
    email: "nguyen@example.com",
    membership: "VIP", // Cấp bậc thành viên
    isU23: true, // Trạng thái U23
    totalSpent: 150000, // Tổng số tiền đã tiêu
  };

  // Giả sử đây là lịch sử đặt vé và lịch sử đặt đồ ăn
  const bookingHistory = [
    { id: 1, date: "2024-12-01", movie: "Avengers", showDate: "2024-12-02", showTime: "18:00", seats: "A1, A2", status: "Đã xác nhận" },
    { id: 2, date: "2024-12-05", movie: "Spider-Man", showDate: "2024-12-06", showTime: "20:00", seats: "B1", status: "Chưa xác nhận" },
  ];

  const foodOrderHistory = [
    { orderId: 1, orderDate: "2024-12-01", product: "Bắp rang bơ", totalPrice: 50000, status: "Đã giao" },
    { orderId: 2, orderDate: "2024-12-05", product: "Nước ngọt", totalPrice: 20000, status: "Đang xử lý" },
  ];

  const handleLogout = () => {
    console.log("Đăng xuất");
    navigate("/login"); // Chuyển hướng đến trang login
  };

  const handleNavigate = (path) => {
    navigate(path); // Chuyển đến đường dẫn tương ứng
  };

  return (
    <div>
    {/* Header */}
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
          <li onClick={() => handleNavigate("/pricing")}>Giá vé</li>
          <li className="logout-button" onClick={handleLogout}>
            <FontAwesomeIcon icon={faRightFromBracket} /> Đăng xuất
          </li>
        </ul>
      </nav>
    </header>

      
      <h1>Thông tin tài khoản</h1>


      {/* Mục 1: Thông tin cá nhân */}
      <section className="personal-info">
        <h2>Thông tin cá nhân</h2>
        <div className="info-item">
          <img src={userInfo.avatar} alt="Avatar" className="avatar" />
          <div className="user-details">
            <p><strong>Họ và tên:</strong> {userInfo.fullName}</p>
            <p><strong>Tuổi:</strong> {userInfo.age}</p>
            <p><strong>Email:</strong> {userInfo.email}</p>
            <p><strong>Cấp bậc thành viên:</strong> {userInfo.membership}</p>
            <p><strong>Trạng thái U23:</strong> {userInfo.isU23 ? "Có" : "Không"}</p>
            <p><strong>Tổng số tiền đã tiêu:</strong> {userInfo.totalSpent.toLocaleString()} VND</p>
          </div>
        </div>
      </section>

      {/* Mục 2: Lịch sử đặt vé */}
      <section className="booking-history">
        <h2>Lịch sử đặt vé</h2>
        <table>
          <thead>
            <tr>
              <th>ID Đặt Vé</th>
              <th>Ngày Đặt</th>
              <th>Tên Phim</th>
              <th>Ngày Chiếu</th>
              <th>Giờ Chiếu</th>
              <th>Số Ghế</th>
              <th>Trạng Thái</th>
            </tr>
          </thead>
          <tbody>
            {bookingHistory.map((booking) => (
              <tr key={booking.id}>
                <td>{booking.id}</td>
                <td>{booking.date}</td>
                <td>{booking.movie}</td>
                <td>{booking.showDate}</td>
                <td>{booking.showTime}</td>
                <td>{booking.seats}</td>
                <td>{booking.status}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </section>

      {/* Mục 3: Lịch sử đặt đồ ăn */}
      <section className="food-order-history">
        <h2>Lịch sử đặt đồ ăn</h2>
        <table>
          <thead>
            <tr>
              <th>ID Đơn Hàng</th>
              <th>Ngày Đặt</th>
              <th>Sản Phẩm</th>
              <th>Tổng Tiền</th>
              <th>Trạng Thái</th>
            </tr>
          </thead>
          <tbody>
            {foodOrderHistory.map((order) => (
              <tr key={order.orderId}>
                <td>{order.orderId}</td>
                <td>{order.orderDate}</td>
                <td>{order.product}</td>
                <td>{order.totalPrice.toLocaleString()} VND</td>
                <td>{order.status}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </section>
    </div>
  );
};

export default Account;
