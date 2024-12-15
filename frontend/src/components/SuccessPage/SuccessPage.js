import React from "react";
import { useLocation, useNavigate } from "react-router-dom";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./SuccessPage.css";

const SuccessPage = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const formatTime = (time) => {
    const [hours, minutes] = time.split(":"); // Lấy giờ và phút
    return `${hours}:${minutes}`;
  };

  // Định dạng ngày chiếu (dd/mm/yyyy)
  const formatDate = (date) => {
    const [year, month, day] = date.split("-"); // Tách chuỗi yyyy-mm-dd
    return `${day}/${month}/${year}`;
  };
  // Lấy dữ liệu từ state
  const { userInfo, paymentId, selectedSeats, totalPrice, scheduleInfo } =
    location.state || {};

  const handleBackToHome = () => {
    navigate("/"); // Điều hướng về trang chủ
  };

  return (
    <>
      <Header />
      <div className="success-page-wrapper">
        <div className="success-page-container">
          <h1>🎉 Thanh Toán Thành Công! 🎉</h1>
          <p className="success-message">
            Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!
          </p>

          <div className="success-user-info">
            <h2>Thông Tin Người Dùng</h2>
            <p>
              <strong>Họ Tên:</strong> {userInfo?.name || "Không có thông tin"}
            </p>
            <p>
              <strong>Email:</strong> {userInfo?.email || "Không có thông tin"}
            </p>
            <p>
              <strong>Số Điện Thoại:</strong>{" "}
              {userInfo?.phone_number || "Không có thông tin"}
            </p>
          </div>

          <div className="success-payment-info">
            <h2>Thông Tin Đơn Thanh Toán</h2>
            <p>
              <strong>Mã Giao Dịch:</strong> {paymentId || "Không có thông tin"}
            </p>
            <p>
              <strong>Số Tiền Đã Thanh Toán:</strong>{" "}
              {totalPrice?.toLocaleString() || "Không có thông tin"} VND
            </p>
            <p>
              <strong>Ghế Đã Đặt:</strong>{" "}
              {selectedSeats?.join(", ") || "Không có thông tin"}
            </p>
          </div>

          <div className="success-schedule-info">
            <h2>Thông Tin Lịch Chiếu</h2>
            <p>
              <strong>Tên Phim:</strong>{" "}
              {scheduleInfo?.movie_title || "Không có thông tin"}
            </p>
            <p>
              <strong>Rạp:</strong>{" "}
              {scheduleInfo?.theater || "Không có thông tin"}
            </p>
            <p>
              <strong>Ngày Chiếu:</strong> {formatDate(scheduleInfo.show_date)}
            </p>
            <p>
              <strong>Giờ Chiếu:</strong> {formatTime(scheduleInfo.show_time)}
            </p>
          </div>

          <button className="back-home-button" onClick={handleBackToHome}>
            Quay Lại Trang Chủ
          </button>
        </div>
      </div>
      <Footer />
    </>
  );
};

export default SuccessPage;
