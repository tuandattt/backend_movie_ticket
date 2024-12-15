import React, { useState, useEffect } from "react";
import "./MovieList.css";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { useNavigate } from "react-router-dom";

const MovieList = ({ movies, currentTab }) => {
  const [showPopup, setShowPopup] = useState(false);
  const [scheduleData, setScheduleData] = useState([]);
  const [selectedMovieTitle, setSelectedMovieTitle] = useState("");
  const [selectedDate, setSelectedDate] = useState(null); // Ngày được chọn
  const [theaterName, setTheaterName] = useState(""); // Nếu cần hiển thị tên rạp

  const navigate = useNavigate();
<<<<<<< HEAD
=======

>>>>>>> 408a5811cc1bdb3635d10bc8a85dfb6abb215592
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const handleMovieClick = (movie) => {
    navigate(`/movie/${movie.movie_id}`, { state: { movie } }); // Truyền dữ liệu qua state
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

  const handleBuyTicket = async (movie_id, title) => {
    try {
      const response = await fetch(
        `http://localhost/web-project/backend/api/fetch_schedules.php?movie_id=${movie_id}`
      );
      const data = await response.json();
      setScheduleData(data);
      setSelectedMovieTitle(title);

      // Lấy rạp từ dữ liệu (giả sử tất cả suất chiếu chung một rạp, nếu nhiều rạp thì tùy logic)
      if (data.length > 0) {
        setTheaterName(data[0].theater);
      }

      // Chọn ngày mặc định là ngày đầu tiên trong data
      const uniqueDates = [...new Set(data.map((s) => s.show_date))];
      if (uniqueDates.length > 0) {
        setSelectedDate(uniqueDates[0]);
      }

      setShowPopup(true);
    } catch (error) {
      console.error("Lỗi khi lấy lịch chiếu:", error);
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
        <div key={movie.movie_id} className="movie-item">
          <img src={movie.poster} alt={movie.title} />
          <div className="movie-item-content">
          <h3
              onClick={() => handleMovieClick(movie)}
              style={{ cursor: "pointer", color: "blue", textDecoration: "underline" }}
            >
              {movie.title}
            </h3>
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
                onClick={() => handleBuyTicket(movie.movie_id, movie.title)}
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
                  onClick={() =>
                    navigate(`/booking?schedule_id=${sch.schedule_id}`)
                  }
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
