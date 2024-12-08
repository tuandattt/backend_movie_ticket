import React from "react";
import "./MovieList.css";

const MovieList = ({ movies, currentTab }) => {
  // Đảm bảo nhận currentTab từ props
  if (movies.length === 0) {
    // Trường hợp không có phim
    return <div className="no-movies">Không có phim nào để hiển thị.</div>;
  }

  // Hàm định dạng ngày tháng theo dạng dd/mm/yyyy
  const formatDate = (dateString) => {
    const date = new Date(dateString); // Chuyển chuỗi ngày thành đối tượng Date
    const day = String(date.getDate()).padStart(2, "0"); // Lấy ngày và thêm số 0 nếu cần
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Lấy tháng (lưu ý tháng trong Date bắt đầu từ 0)
    const year = date.getFullYear(); // Lấy năm

    return `${day}/${month}/${year}`; // Trả về ngày ở dạng dd/mm/yyyy
  };

  // Trường hợp có phim
  return (
    <div className="movie-list">
      {movies.map((movie) => (
        <div key={movie.movie_id} className="movie-item">
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
              </p> // Hiển thị ngày khởi chiếu theo định dạng dd/mm/yyyy
            ) : (
              <button className="buy-ticket-button"><strong>MUA VÉ</strong></button> // Hiển thị nút mua vé nếu là phim đang chiếu
            )}
          </div>
        </div>
      ))}
    </div>
  );
};

export default MovieList;
