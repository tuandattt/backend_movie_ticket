import React, { useState, useEffect } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./Payment.css";

const Payment = () => {
  const location = useLocation();
  const { scheduleInfo, selectedSeats, totalPrice } = location.state || {}; // Lấy dữ liệu từ state

  const [userInfo, setUserInfo] = useState(null); // Thông tin người dùng
  const [discount, setDiscount] = useState(0); // Giảm giá
  const [discountDetails, setDiscountDetails] = useState([]);

  const formatTime = (time) => {
    const [hours, minutes] = time.split(":"); // Lấy giờ và phút
    return `${hours}:${minutes}`;
  };

  // Định dạng ngày chiếu (dd/mm/yyyy)
  const formatDate = (date) => {
    const [year, month, day] = date.split("-"); // Tách chuỗi yyyy-mm-dd
    return `${day}/${month}/${year}`;
  };

  // Tính toán giảm giá
  const calculateDiscount = (user, total) => {
    let discountRate = 0;
    const reasons = [];

    if (user.is_u23_confirmed === "yes") {
      discountRate += 0.18; // Giảm 18% nếu U23
      reasons.push({ label: "U23", rate: 18, amount: total * 0.18 });
    }

    if (user.membership_level === "silver") {
      discountRate += 0.05; // Giảm 5% nếu silver
      reasons.push({ label: "Silver", rate: 5, amount: total * 0.05 });
    } else if (user.membership_level === "gold") {
      discountRate += 0.1; // Giảm 10% nếu gold
      reasons.push({ label: "Gold", rate: 10, amount: total * 0.1 });
    } else if (user.membership_level === "platinum") {
      discountRate += 0.15; // Giảm 15% nếu platinum
      reasons.push({ label: "Platinum", rate: 15, amount: total * 0.15 });
    }

    setDiscount(total * discountRate);
    setDiscountDetails(reasons);
  };

  useEffect(() => {
    // Lấy thông tin người dùng
    const fetchUserInfo = async () => {
      try {
        const response = await fetch(
          "http://localhost/web-project/backend/api/get_user_info.php",
          { credentials: "include" }
        );
        const data = await response.json();

        if (data.status === "success") {
          setUserInfo(data.data); // Sử dụng key 'data'
          calculateDiscount(data.data, totalPrice); // Tính toán giảm giá
        } else {
          console.error(data.message);
        }
      } catch (error) {
        console.error("Lỗi khi lấy thông tin người dùng:", error);
      }
    };

    fetchUserInfo();
  }, [totalPrice]);

  const seatTypes = {
    regular: { count: 0, price: 50000 },
    vip: { count: 0, price: 55000 },
    double: { count: 0, price: 110000 },
  };

  selectedSeats.forEach((seat) => {
    const row = seat.charAt(0).toUpperCase();
    if (["A", "B", "C"].includes(row)) {
      seatTypes.regular.count++;
    } else if (["D", "E", "G", "H", "I"].includes(row)) {
      seatTypes.vip.count++;
    } else if (row === "K") {
      seatTypes.double.count++;
    }
  });

  const renderSeatInfo = () => {
    const seatInfo = [];
    if (seatTypes.regular.count > 0) {
      seatInfo.push(
        <div className="payment-seat-row" key="regular">
          <span>Ghế Thường</span>
          <span>
            {seatTypes.regular.count} x{" "}
            {seatTypes.regular.price.toLocaleString()}
          </span>
          <span>
            ={" "}
            {(
              seatTypes.regular.count * seatTypes.regular.price
            ).toLocaleString()}{" "}
            VND
          </span>
        </div>
      );
    }
    if (seatTypes.vip.count > 0) {
      seatInfo.push(
        <div className="payment-seat-row" key="vip">
          <span>Ghế VIP</span>
          <span>
            {seatTypes.vip.count} x {seatTypes.vip.price.toLocaleString()}
          </span>
          <span>
            = {(seatTypes.vip.count * seatTypes.vip.price).toLocaleString()} VND
          </span>
        </div>
      );
    }
    if (seatTypes.double.count > 0) {
      seatInfo.push(
        <div className="payment-seat-row" key="double">
          <span>Ghế Đôi</span>
          <span>
            {seatTypes.double.count} x {seatTypes.double.price.toLocaleString()}
          </span>
          <span>
            ={" "}
            {(seatTypes.double.count * seatTypes.double.price).toLocaleString()}{" "}
            VND
          </span>
        </div>
      );
    }
    return seatInfo;
  };

  const renderDiscountDetails = () => {
    return discountDetails.map((detail, index) => (
      <div key={index} className="discount-detail-row">
        <span>
          {detail.label} -{detail.rate}%:
        </span>
        <span>{detail.amount.toLocaleString()} VND</span>
      </div>
    ));
  };

  const navigate = useNavigate();
  const handleContinue = () => {
    navigate("/next-step", {
      state: { scheduleInfo, selectedSeats, totalPrice },
    });
  };

  if (!scheduleInfo) {
    return <div>Không có dữ liệu thanh toán</div>;
  }

  return (
    <>
      <Header />
      <div className="payment-wrapper">
        <div className="payment-container">
          <div className="payment-details">
            <div className="payment-user-info">
              {userInfo ? (
                <>
                  <p>
                    <strong>Họ Tên:</strong>{" "}
                    {userInfo.name || "Không có thông tin"}
                  </p>
                  <p>
                    <strong>Số Điện Thoại:</strong>{" "}
                    {userInfo.phone_number || "Không có thông tin"}
                  </p>
                  <p>
                    <strong>Email:</strong>{" "}
                    {userInfo.email || "Không có thông tin"}
                  </p>
                </>
              ) : (
                <p>Đang tải...</p>
              )}
            </div>
            <div className="payment-seat-info">
              <h3>
                Ghế Đã Chọn{" "}
                {selectedSeats.length > 0 && (
                  <span>({selectedSeats.join(", ")})</span>
                )}
              </h3>
              <div>{renderSeatInfo()}</div>
            </div>
            <div className="payment-summary">
              <div className="payment-summary-row">
                <span>Tổng tiền:</span>
                <span className="payment-total-amount">
                  {totalPrice.toLocaleString()} VND
                </span>
              </div>

              {discountDetails.length > 0 && (
                <div className="discount-details">
                  {renderDiscountDetails()}
                </div>
              )}
              <div className="payment-summary-row">
                <span>Số tiền được giảm:</span>
                <span className="payment-discount">
                  - {discount.toLocaleString()} VND
                </span>
              </div>
              <div className="payment-summary-row">
                <span>Số tiền cần thanh toán:</span>
                <span className="payment-final-amount">
                  {(totalPrice - discount).toLocaleString()} VND
                </span>
              </div>
            </div>
          </div>
          <div className="payment-movie-info">
            <div className="payment-movie-details">
              <img
                src={scheduleInfo.poster}
                alt={scheduleInfo.movie_title}
                className="payment-movie-poster"
              />
              <div className="payment-movie-info">
                <h2>{scheduleInfo.movie_title}</h2>
                <p>
                  <strong>Thể loại:</strong> {scheduleInfo.genres}
                </p>
                <p>
                  <strong>Thời lượng:</strong> {scheduleInfo.duration} phút
                </p>
                <p>
                  <strong>Rạp chiếu:</strong> {scheduleInfo.theater}
                </p>
                <p>
                  <strong>Ngày chiếu:</strong>{" "}
                  {formatDate(scheduleInfo.show_date)}
                </p>
                <p>
                  <strong>Giờ chiếu:</strong>{" "}
                  {formatTime(scheduleInfo.show_time)}
                </p>
              </div>
              <div className="payment-actions">
                <button
                  className="payment-back-button"
                  onClick={() => navigate(-1)}
                >
                  Quay Lại
                </button>
                <button
                  className="payment-continue-button"
                  onClick={handleContinue}
                >
                  Tiếp Tục
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Footer />
    </>
  );
};

export default Payment;
