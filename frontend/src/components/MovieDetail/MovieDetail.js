import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./MovieDetail.css";

const MovieDetail = () => {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const movieId = params.get("movie_id");

  const [movieDetails, setMovieDetails] = useState(null);

  // Định dạng ngày chiếu (dd/mm/yyyy)
  const formatDate = (date) => {
    const [year, month, day] = date.split("-"); // Tách chuỗi yyyy-mm-dd
    return `${day}/${month}/${year}`;
  };

  useEffect(() => {
    if (movieId) {
      fetchMovieDetails();
    }
  }, [movieId]);

  const fetchMovieDetails = async () => {
    try {
      const response = await fetch(
        `http://localhost/web-project/backend/api/fetch_movie_details.php?movie_id=${movieId}`
      );
      const result = await response.json();
      if (result.status === "success") {
        setMovieDetails(result.data);
      } else {
        console.error("Lỗi khi lấy chi tiết phim");
      }
    } catch (error) {
      console.error("Lỗi kết nối API chi tiết phim:", error);
    }
  };

  return (
    <>
      <Header />
      <div className="movie-detail-container">
        {movieDetails && (
          <>
            {/* Thông tin chi tiết phim */}
            <div className="movie-detail-info">
              <img
                src={`http://localhost/web-project/backend/assets/images/${movieDetails.poster}`}
                alt={movieDetails.title}
                className="movie-detail-poster"
              />
              <div className="movie-detail-details">
                <h1>{movieDetails.title}</h1>
                <p>{movieDetails.description}</p>
                <p>
                  <strong>ĐẠO DIỄN:</strong> {movieDetails.director}
                </p>
                <p>
                  <strong>DIỄN VIÊN:</strong> {movieDetails.actors}
                </p>
                <p>
                  <strong>THỂ LOẠI:</strong> {movieDetails.genres.join(", ")}
                </p>
                <p>
                  <strong>THỜI LƯỢNG:</strong> {movieDetails.duration} phút
                </p>
                <p>
                  <strong>NGÀY KHỞI CHIẾU:</strong>{" "}
                  {formatDate(movieDetails.release_date)}
                </p>
              </div>
            </div>

            {/* Video Trailer */}
            <div className="movie-trailer">
              <h2 className="trailer-title">TRAILER</h2>
              <div className="trailer-container">
                <iframe
                  width="100%"
                  height="450"
                  src={movieDetails.trailer_link} // Link trailer
                  title="Movie Trailer"
                  frameBorder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowFullScreen
                ></iframe>
              </div>
            </div>
          </>
        )}
      </div>
      <Footer />
    </>
  );
};

export default MovieDetail;
