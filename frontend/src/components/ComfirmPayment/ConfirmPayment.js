import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import { QRCodeCanvas } from "qrcode.react";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./ConfirmPayment.css";

const ConfirmPayment = () => {
  const location = useLocation();
  const navigate = useNavigate();

  const { paymentId, totalPrice, selectedSeats, scheduleInfo, userInfo } =
    location.state || {};

  const [countdown, setCountdown] = useState(300); // 300 giây = 5 phút
  const [paymentStatus, setPaymentStatus] = useState("pending"); // Trạng thái giao dịch
  const [isProcessing, setIsProcessing] = useState(false); // Trạng thái xử lý giao dịch

  // Xử lý đếm ngược
  useEffect(() => {
    const timer = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(timer); // Dừng bộ đếm
          if (paymentStatus === "pending") expireTransaction(); // Xử lý nếu hết hạn
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer); // Dọn sạch interval khi component bị unmount
  }, [countdown, paymentStatus]);

  // Kiểm tra trạng thái giao dịch
  useEffect(() => {
    const checkPaymentStatus = async () => {
      try {
        const response = await fetch(
          `http://localhost/web-project/backend/api/get_payment_status.php?payment_id=${paymentId}`,
          { method: "GET", credentials: "include" }
        );
        const data = await response.json();
        if (data.status === "success" && data.payment_status === "confirmed") {
          setPaymentStatus("confirmed");
          alert("Thanh toán thành công!");
          navigate("/success", {
            state: {
              paymentId,
              totalPrice,
              selectedSeats,
              scheduleInfo,
              userInfo,
            },
          });   // Điều hướng sang trang thông báo thành công
        }
      } catch (error) {
        console.error("Lỗi khi kiểm tra trạng thái giao dịch:", error);
      }
    };

    const interval = setInterval(checkPaymentStatus, 3000); // Kiểm tra mỗi 3 giây
    return () => clearInterval(interval);
  }, [paymentId, navigate]);

  // Hàm xử lý hủy giao dịch do hết thời gian
  const expireTransaction = async () => {
    try {
      await fetch(
        `http://localhost/web-project/backend/api/expired_payment.php?payment_id=${paymentId}`,
        {
          method: "POST",
          credentials: "include",
          headers: { "Content-Type": "application/json" },
        }
      );
      alert("Giao dịch đã hết hạn.");
      navigate("/"); // Chuyển về trang chủ
    } catch (error) {
      console.error("Lỗi khi đánh dấu giao dịch hết hạn:", error);
    }
  };

  // Hàm xử lý hủy giao dịch do người dùng nhấn "Hủy"
  const cancelTransaction = async () => {
    try {
      await fetch(
        `http://localhost/web-project/backend/api/cancel_payment.php?payment_id=${paymentId}`,
        { method: "POST" }
      );
      alert("Giao dịch đã bị hủy.");
      navigate("/"); // Chuyển về trang chủ
    } catch (error) {
      console.error("Lỗi khi hủy giao dịch:", error);
    }
  };

  return (
    <>
      <Header />
      <div className="confirm-payment-wrapper">
        <div className="confirm-payment-container">
          <h2>Xác Nhận Thanh Toán</h2>
          <div className="qr-code-section">
            <QRCodeCanvas
              value={`http://192.168.1.2/web-project/backend/api/confirm_payment.php?payment_id=${paymentId}&schedule_id=${
                scheduleInfo.schedule_id
              }&selected_seats=${selectedSeats.join(",")}`}
              size={256}
            />
          </div>
          <div className="payment-details">
            <p>
              <strong>Số tiền:</strong> {totalPrice.toLocaleString()} VND
            </p>
            <p>
              <strong>Thời gian còn lại:</strong> {Math.floor(countdown / 60)}:
              {countdown % 60 < 10 ? `0${countdown % 60}` : countdown % 60}
            </p>
          </div>
          <div className="payment-actions">
            <button
              className="cancel-button"
              onClick={cancelTransaction}
              disabled={isProcessing}
            >
              Hủy Giao Dịch
            </button>
          </div>
        </div>
      </div>
      <Footer />
    </>
  );
};

export default ConfirmPayment;
