import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import "./BookingPage.css";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";

const BookingPage = () => {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const scheduleId = params.get("schedule_id"); // Lấy schedule_id từ URL

  const [scheduleInfo, setScheduleInfo] = useState(null);
  const [selectedSeats, setSelectedSeats] = useState([]);

  const formatTime = (time) => {
    const [hours, minutes] = time.split(":"); // Lấy giờ và phút
    return `${hours}:${minutes}`;
  };

  // Định dạng ngày chiếu (dd/mm/yyyy)
  const formatDate = (date) => {
    const [year, month, day] = date.split("-"); // Tách chuỗi yyyy-mm-dd
    return `${day}/${month}/${year}`; // Trả về dd/mm/yyyy
  };

  // Danh sách ghế thường (A-I như trước)
  const rows = {
    A: [
      "A12",
      "A11",
      "A10",
      "A9",
      "A8",
      "A7",
      "A6",
      "A5",
      "A4",
      "A3",
      "A2",
      "A1",
    ],
    B: [
      "B12",
      "B11",
      "B10",
      "B9",
      "B8",
      "B7",
      "B6",
      "B5",
      "B4",
      "B3",
      "B2",
      "B1",
    ],
    C: [
      "C12",
      "C11",
      "C10",
      "C9",
      "C8",
      "C7",
      "C6",
      "C5",
      "C4",
      "C3",
      "C2",
      "C1",
    ],
    D: [
      "D12",
      "D11",
      "D10",
      "D9",
      "D8",
      "D7",
      "D6",
      "D5",
      "D4",
      "D3",
      "D2",
      "D1",
    ],
    E: [
      "E12",
      "E11",
      "E10",
      "E9",
      "E8",
      "E7",
      "E6",
      "E5",
      "E4",
      "E3",
      "E2",
      "E1",
    ],
    G: [
      "G12",
      "G11",
      "G10",
      "G9",
      "G8",
      "G7",
      "G6",
      "G5",
      "G4",
      "G3",
      "G2",
      "G1",
    ],
    H: [
      "H12",
      "H11",
      "H10",
      "H9",
      "H8",
      "H7",
      "H6",
      "H5",
      "H4",
      "H3",
      "H2",
      "H1",
    ],
    I: [
      "I12",
      "I11",
      "I10",
      "I9",
      "I8",
      "I7",
      "I6",
      "I5",
      "I4",
      "I3",
      "I2",
      "I1",
    ],
    // Hàng K là ghế đôi, mỗi phần tử là một cặp "K1-2"
    // Thứ tự sắp xếp: K9-10, K7-8, K5-6, K3-4, K1-2 để giống hình
    K: ["K10-K9", "K8-K7", "K6-K5", "K4-K3", "K2-K1"],
  };

  // Tạo danh sách tất cả ghế cho logic
  // Gồm A-I như cũ + K1 đến K10
  const allSeats = [
    ...rows.A,
    ...rows.B,
    ...rows.C,
    ...rows.D,
    ...rows.E,
    ...rows.G,
    ...rows.H,
    ...rows.I,
    ...rows.K,
  ];

  const [seatsList, setSeatsList] = useState([]);

  useEffect(() => {
    console.log("Starting fetch for schedule...");

    const initialSeats = allSeats.map((seat) => ({
      seat,
      status: "available",
    }));
    setSeatsList(initialSeats);

    if (!scheduleId) {
      console.error("Invalid scheduleId");
      return;
    }

    // Fetch thông tin lịch chiếu
    fetch(
      `http://localhost/web-project/backend/api/fetch_schedule_by_id.php?schedule_id=${scheduleId}`
    )
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        console.log("Fetched schedule data:", data);
        if (JSON.stringify(scheduleInfo) !== JSON.stringify(data)) {
          setScheduleInfo(data);
        }
      })
      .catch((err) => console.error("Error fetching schedule info:", err));

    // Fetch ghế đã bán
    fetch(
      `http://localhost/web-project/backend/api/fetch_booked_seats.php?schedule_id=${scheduleId}`
    )
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((bookedSeats) => {
        console.log("Fetched booked seats:", bookedSeats);
        setSeatsList((prev) =>
          prev.map((seatObj) =>
            bookedSeats.includes(seatObj.seat)
              ? { ...seatObj, status: "sold" }
              : seatObj
          )
        );
      })
      .catch((err) => console.error("Error fetching booked seats:", err));
  }, [scheduleId]); // Chỉ cần `scheduleId` làm dependency

  const getSeatStatus = (seatName) => {
    const seatObj = seatsList.find((s) => s.seat === seatName);
    if (!seatObj) return "";
    if (selectedSeats.includes(seatName)) return "selected";
    return seatObj.status === "sold" ? "sold" : "";
  };

  const toggleSeatSelectionByName = (seatName) => {
    const seatObj = seatsList.find((s) => s.seat === seatName);
    if (!seatObj || seatObj.status === "sold") return;
    if (selectedSeats.includes(seatName)) {
      setSelectedSeats(selectedSeats.filter((s) => s !== seatName));
    } else {
      setSelectedSeats([...selectedSeats, seatName]);
    }
  };

  const renderSeats = (seatArray) => {
    return seatArray.map((seatName) => {
      const status = getSeatStatus(seatName);
      let className = "seat";
      if (seatName.includes("-")) {
        // Ghế đôi, làm rộng hơn
        className += " double";
      }
      if (status === "sold") className += " sold";
      else if (status === "selected") className += " selected";

      return (
        <div
          key={seatName}
          className={className}
          onClick={() => toggleSeatSelectionByName(seatName)}
        >
          {seatName}
        </div>
      );
    });
  };

  const getSeatPrice = (seatNumber) => {
    const row = seatNumber.charAt(0).toUpperCase();
    const regularPrice = 50000;
    const vipPrice = 55000;
    const doubleSeatPrice = 110000;

    if (["A", "B", "C"].includes(row)) {
      return regularPrice;
    } else if (["D", "E", "G", "H", "I"].includes(row)) {
      return vipPrice;
    } else if (row === "K") {
      return doubleSeatPrice;
    }
    return 0;
  };

  const totalPrice = selectedSeats.reduce(
    (sum, seat) => sum + getSeatPrice(seat),
    0
  );

  if (!scheduleInfo) {
    return <div>Loading...</div>;
  }

  return (
    <>
      <Header />
      <div className="booking-page">
        <div className="booking-container">
          <div className="seat-map-container">
            <div className="seat-map">
              <div className="screen">MÀN HÌNH CHIẾU</div>

              {/* Mỗi hàng là một seat-row */}
              <div className="seat-row">{renderSeats(rows.A)}</div>
              <div className="seat-row">{renderSeats(rows.B)}</div>
              <div className="seat-row">{renderSeats(rows.C)}</div>
              <div className="seat-row">{renderSeats(rows.D)}</div>
              <div className="seat-row">{renderSeats(rows.E)}</div>
              <div className="seat-row">{renderSeats(rows.G)}</div>
              <div className="seat-row">{renderSeats(rows.H)}</div>
              <div className="seat-row">{renderSeats(rows.I)}</div>
              <div className="seat-row">{renderSeats(rows.K)}</div>

              <div className="legend">
                <span>
                  <div className="legend-seat"></div> Ghế trống
                </span>
                <span>
                  <div className="legend-seat selected"></div> Ghế đang chọn
                </span>
                <span>
                  <div className="legend-seat sold"></div> Ghế đã bán
                </span>
              </div>
            </div>
            <div className="booking-info">
              <div className="movie-details">
                <img
                  src={scheduleInfo.poster}
                  alt={scheduleInfo.movie_title}
                  className="movie-poster"
                />
                <div className="movie-info">
                  <h2 className="movie-title">{scheduleInfo.movie_title}</h2>
                  <p className="movie-subtitle">
                    {scheduleInfo.subtitle || "2D Phụ đề"}
                  </p>
                  <p>
                    <strong>Thể loại:</strong> {scheduleInfo.genres}
                  </p>
                  <p>
                    <strong>Thời lượng:</strong> {scheduleInfo.duration} phút
                  </p>
                </div>
              </div>
              <hr />
              <div className="schedule-details">
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
                <p>
                  <strong>Ghế đã chọn:</strong>{" "}
                  {selectedSeats.join(", ") || "Chưa chọn"}
                </p>
                <p>
                  <strong>Tổng tiền:</strong> {totalPrice.toLocaleString()} VND
                </p>
              </div>
              <button className="continue-button">Tiếp tục</button>
            </div>
          </div>
        </div>
      </div>
      <Footer /> {/* Footer hiển thị dưới cùng */}
    </>
  );
};

export default BookingPage;
