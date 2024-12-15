import React from "react";
import { useLocation, useParams } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faRightFromBracket } from "@fortawesome/free-solid-svg-icons";
import { useNavigate } from "react-router-dom";
import "./MovieDetails.css";

function MovieDetail() {
  const { state } = useLocation(); // Nhận state được truyền từ MovieList
  const { movieId } = useParams(); // Lấy movieId từ URL
  const navigate = useNavigate();

  const handleLogout = () => {
    console.log("Đăng xuất");
    navigate("/login"); // Chuyển hướng đến trang login
  };

  const handleNavigate = (path) => {
    navigate(path); // Chuyển đến đường dẫn tương ứng
  };

  const movie = state?.movie; // Lấy dữ liệu phim từ state

  if (!movie) {
    return <div>Không tìm thấy phim.</div>;
  }

  return (
    <div>
      {/* Header */}
      <header className="header-container">
        <img 
          src="/img/cinema_logo2.png" 
          alt="Logo" 
          className="header-logo"
          onClick={() => navigate("/")} // Điều hướng về trang Home khi click logo
        />
        <nav>
          <ul className="header-menu">
            <li onClick={() => handleNavigate("/showtimes")}>Lịch chiếu</li>
            <li onClick={() => handleNavigate("/pricing")}>Giá vé</li>
            <li onClick={() => handleNavigate("/account")}>Thông tin tài khoản</li>
            <li className="logout-button" onClick={handleLogout}>
              <FontAwesomeIcon icon={faRightFromBracket} /> Đăng xuất
            </li>
          </ul>
        </nav>
      </header>

      {/* Movie Details */}
      <div className="movie-detail">
        <img src={movie.poster} alt={movie.title} style={{ width: "300px" }} />
        <h1>{movie.title}</h1>
        <p><strong>Thể loại:</strong> {movie.genres}</p>
        <p><strong>Thời lượng:</strong> {movie.duration} phút</p>
        <p><strong>Mô tả:</strong> {movie.description}</p>
        <p><strong>Đạo diễn:</strong> {movie.director}</p>
        <p><strong>Diễn viên:</strong> {movie.cast}</p>
        <p><strong>Ngày phát hành:</strong> {movie.release_date}</p>
      </div>
    </div>
  );
}

export default MovieDetail;
