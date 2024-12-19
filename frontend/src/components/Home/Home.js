import React, { useEffect, useState, useContext } from "react";
import { useNavigate } from "react-router-dom";
import Header from "./Header/Header";
import Slider from "./Slider/Slider";
import MovieList from "./MovieList/MovieList";
import Footer from "./Footer/Footer";
import ChatBox from "./ChatBox/ChatBox"; // Import ChatBox
import { AuthContext } from "../../context/AuthContext"; // Context quản lý trạng thái đăng nhập

function Home() {
  const [movies, setMovies] = useState([]); // Dữ liệu phim
  const [currentTab, setCurrentTab] = useState("now_showing"); // Trạng thái tab

  const { isLoggedIn, userId } = useContext(AuthContext); // Lấy thông tin đăng nhập từ context
  const navigate = useNavigate();

  useEffect(() => {
    // Fetch dữ liệu từ backend
    fetch("http://localhost/web-project/backend/api/get_movies.php")
      .then((response) => response.json())
      .then((data) => setMovies(data))
      .catch((error) => console.error("Lỗi khi lấy dữ liệu phim:", error));
  }, []);

  const filteredMovies = movies.filter((movie) => movie.status === currentTab);

  return (
    <div>
      <Header
        isLoggedIn={isLoggedIn}
        username={isLoggedIn ? "Người dùng" : ""}
      />
      <Slider />
      <div className="content">
        {/* Tabs điều hướng */}
        <div className="tabs-wrapper">
          <div className="tabs">
            <button
              className={`tab ${currentTab === "now_showing" ? "active" : ""}`}
              onClick={() => setCurrentTab("now_showing")}
            >
              PHIM ĐANG CHIẾU
            </button>
            <button
              className={`tab ${currentTab === "coming_soon" ? "active" : ""}`}
              onClick={() => setCurrentTab("coming_soon")}
            >
              PHIM SẮP CHIẾU
            </button>
          </div>
        </div>
        {/* Danh sách phim */}
        <MovieList movies={filteredMovies} currentTab={currentTab} />
      </div>
      <Footer />
      {/* Hiển thị ChatBox */}
      {isLoggedIn && (
        <div className="chatbox-wrapper">
          <ChatBox userId={userId} adminId={1} />
        </div>
      )}
    </div>
  );
}

export default Home;
