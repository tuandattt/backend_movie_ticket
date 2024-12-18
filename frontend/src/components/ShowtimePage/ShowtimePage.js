import React, { useState, useEffect, useContext } from "react";
import { useNavigate } from "react-router-dom"; // Import useNavigate
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./ShowtimePage.css";
import { AuthContext } from "../../context/AuthContext";

const ShowtimePage = () => {
  const [showtimes, setShowtimes] = useState([]);
  const [dates, setDates] = useState([]);
  const [selectedDate, setSelectedDate] = useState("");
  const { isLoggedIn } = useContext(AuthContext);

  const navigate = useNavigate(); // Hook điều hướng

  const formatTime = (time) => {
    const [hours, minutes] = time.split(":"); // Lấy giờ và phút
    return `${hours}:${minutes}`;
  };

  useEffect(() => {
    const currentDate = new Date();
    const weekdayMap = ["CN", "T2", "T3", "T4", "T5", "T6", "T7"]; // Đổi T1 thành CN

    const upcomingDates = Array.from({ length: 7 }, (_, i) => {
      const date = new Date();
      date.setDate(currentDate.getDate() + i);
      return {
        formatted: date.toISOString().split("T")[0],
        display: `${date.getDate()}/${date.getMonth() + 1} - ${
          weekdayMap[date.getDay()]
        }`,
      };
    });

    setDates(upcomingDates);
    setSelectedDate(upcomingDates[0].formatted);
  }, []);

  useEffect(() => {
    fetchShowtimes();
  }, [selectedDate]);

  const fetchShowtimes = async () => {
    try {
      const response = await fetch(
        `http://localhost/web-project/backend/api/fetch_showtimes_by_date.php?show_date=${selectedDate}`
      );
      const result = await response.json();
      if (result.status === "success") {
        setShowtimes(result.data);
      } else {
        console.error("Lỗi khi lấy dữ liệu lịch chiếu");
      }
    } catch (error) {
      console.error("Lỗi kết nối API:", error);
    }
  };

  // Xử lý sự kiện khi click vào giờ chiếu
  const handleShowtimeClick = (scheduleId) => {
    if (isLoggedIn) {
      navigate(`/booking?schedule_id=${scheduleId}`); // Điều hướng đến màn hình tài khoản
    } else {
      navigate("/login"); // Điều hướng đến màn hình đăng nhập nếu chưa đăng nhập
    }
  };

  return (
    <>
      <Header />
      <div className="showtime-container">
        {/* Phần chọn ngày */}
        <div className="showtime-date-picker">
          {dates.map((date) => (
            <div
              key={date.formatted}
              className={`showtime-date-item ${
                selectedDate === date.formatted ? "showtime-active-date" : ""
              }`}
              onClick={() => setSelectedDate(date.formatted)}
            >
              {date.display}
            </div>
          ))}
        </div>

        {/* Phần hiển thị phim */}
        <div className="showtime-movie-list">
          {showtimes.length > 0 ? (
            showtimes.map((movie) => (
              <div key={movie.movie_id} className="showtime-movie-card">
                <img
                  src={`http://localhost/web-project/backend/assets/images/${movie.poster}`}
                  alt={movie.title}
                  className="showtime-movie-poster"
                />
                <div className="showtime-movie-details">
                  <h3 className="showtime-movie-title">{movie.title}</h3>
                  <p className="showtime-movie-genres">{movie.genres}</p>
                  <p className="showtime-movie-duration">
                    {movie.duration} phút
                  </p>
                  <div className="showtime-slots-container">
                    {movie.show_times.map((slot) => (
                      <div
                        key={slot.schedule_id}
                        className="showtime-slot-item"
                        onClick={() => handleShowtimeClick(slot.schedule_id)} // Thêm onClick
                      >
                        <span className="showtime-time">
                          {formatTime(slot.show_time)}
                        </span>
                        <span className="showtime-available-seats">
                          {slot.available_seats} ghế trống
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="showtime-no-data">
              Không có lịch chiếu cho ngày này.
            </p>
          )}
        </div>
      </div>
      <Footer />
    </>
  );
};

export default ShowtimePage;
