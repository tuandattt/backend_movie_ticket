import React, { useState, useEffect, useContext } from "react";
import "./MovieList.css";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { useNavigate } from "react-router-dom";
import { AuthContext } from "../../../context/AuthContext";

const MovieList = ({ movies, currentTab }) => {
  const [showPopup, setShowPopup] = useState(false);
  const [scheduleData, setScheduleData] = useState([]);
  const [selectedMovieTitle, setSelectedMovieTitle] = useState("");
  const [selectedDate, setSelectedDate] = useState(null); // Ngày được chọn
  const [theaterName, setTheaterName] = useState(""); // Nếu cần hiển thị tên rạp

  const { isLoggedIn } = useContext(AuthContext);

  const navigate = useNavigate();

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  // Hàm định dạng ngày thành dd/mm - Tx (trong ảnh có dạng 11/12 - T4)
  const formatDateWithWeekday = (dateString) => {
    const date = new Date(dateString);
    const weekdayMap = ["CN", "T2", "T3", "T4", "T5", "T6", "T7"];
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const weekday = weekdayMap[date.getDay()];
    return `${day}/${month} - ${weekday}`;
  };

  const formatTime = (timeString) => {
    // timeString dạng "HH:MM:SS"
    const [hours, minutes] = timeString.split(":");
    return `${hours}:${minutes}`; // Trả về "HH:MM"
  };

  const handleMovieClick = (movie_id) => {
    navigate(`/movie?movie_id=${movie_id}`); // Truyền movie_id dưới dạng query string
  };

  const handleBuyTicket = async (movie_id, title) => {
    try {
      const response = await fetch(
        `http://localhost/web-project/backend/api/fetch_schedules.php?movie_id=${movie_id}`
      );
      const result = await response.json();

      if (result.status === "success" && Array.isArray(result.data)) {
        setScheduleData(result.data); // Cập nhật dữ liệu lịch chiếu
      } else {
        setScheduleData([]); // Gán mảng rỗng nếu không có dữ liệu hợp lệ
      }

      setSelectedMovieTitle(title);

      if (result.data.length > 0) {
        setTheaterName(result.data[0].theater);
        const uniqueDates = [...new Set(result.data.map((s) => s.show_date))];
        if (uniqueDates.length > 0) {
          setSelectedDate(uniqueDates[0]);
        }
      } else {
        setTheaterName(""); // Đặt rạp và ngày mặc định về rỗng nếu không có lịch chiếu
        setSelectedDate(null);
      }

      setShowPopup(true);
    } catch (error) {
      console.error("Lỗi khi lấy lịch chiếu:", error);
      setScheduleData([]); // Đảm bảo state không chứa dữ liệu lỗi
    }
  };

  const closePopup = () => {
    setShowPopup(false);
    setScheduleData([]);
    setSelectedDate(null);
    setTheaterName("");
  };

  if (movies.length === 0) {
    return <div className="no-movies">Không có phim nào để hiển thị.</div>;
  }

  // Nhóm lịch chiếu theo ngày
  const dates = [...new Set(scheduleData.map((s) => s.show_date))];

  // Lấy danh sách suất chiếu theo ngày được chọn
  const filteredSchedules = scheduleData.filter(
    (s) => s.show_date === selectedDate
  );

  return (
    <div className="movie-list">
      {movies.map((movie) => (
        <div
          key={movie.movie_id}
          className="movie-item"
          onClick={() => handleMovieClick(movie.movie_id)}
        >
          <img src={movie.poster} alt={movie.title} />
          <div className="movie-item-content">
            <h3>{movie.title}</h3>
            <p>
              <strong>Thể loại:</strong> {movie.genres}
            </p>
            <p>
              <strong>Thời lượng:</strong> {movie.duration} phút
            </p>
            {currentTab === "coming_soon" ? (
              <p>
                <strong>Ngày khởi chiếu:</strong>{" "}
                {formatDate(movie.release_date)}
              </p>
            ) : (
              <button
                className="buy-ticket-button"
                onClick={(e) => {
                  e.stopPropagation(); // Ngăn sự kiện nổi bọt
                  handleBuyTicket(movie.movie_id, movie.title);
                }}
              >
                <strong>MUA VÉ</strong>
              </button>
            )}
          </div>
        </div>
      ))}
      {showPopup && (
        <div className="popup-overlay">
          <div className="popup-container">
            <button className="close-popup" onClick={closePopup}>
              <FontAwesomeIcon icon={faXmark} />
            </button>
            <h2 className="popup-title">LỊCH CHIẾU - {selectedMovieTitle}</h2>
            {theaterName && <div className="theater-name">{theaterName}</div>}
            {dates.length > 0 ? (
              <div className="date-tabs">
                {dates.map((d) => (
                  <button
                    key={d}
                    className={`date-tab ${d === selectedDate ? "active" : ""}`}
                    onClick={() => setSelectedDate(d)}
                  >
                    {formatDateWithWeekday(d)}
                  </button>
                ))}
              </div>
            ) : (
              <p>Không có ngày chiếu.</p>
            )}

            {filteredSchedules.length > 0 ? (
              <div className="format-type">2D PHỤ ĐỀ</div>
            ) : null}

            <div className="showtimes-container">
              {filteredSchedules.map((sch) => (
                <div
                  key={sch.schedule_id}
                  className="showtime-item"
                  onClick={() => {
                    if (isLoggedIn) {
                      navigate(`/booking?schedule_id=${sch.schedule_id}`);
                    } else {
                      navigate("/login"); // Chuyển đến trang đăng nhập nếu chưa đăng nhập
                    }
                  }}
                >
                  <div className="showtime">{formatTime(sch.show_time)}</div>
                  <div className="available-seats">
                    {sch.available_seats} ghế trống
                  </div>
                </div>
              ))}
            </div>

            {filteredSchedules.length === 0 && (
              <p>Không có lịch chiếu cho phim này.</p>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default MovieList;
