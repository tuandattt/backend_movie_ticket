import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import Header from "./Header/Header";
import Slider from "./Slider/Slider";
import MovieList from "./MovieList/MovieList";
import Footer from "./Footer/Footer";

function Home() {
  const [movies, setMovies] = useState([]); // Dữ liệu phim
  const [currentTab, setCurrentTab] = useState("now_showing"); // Trạng thái tab
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState("");

  const navigate = useNavigate();

  useEffect(() => {
    // Fetch dữ liệu từ backend
    fetch("http://localhost/web-project/backend/api/get_movies.php")
      .then((response) => response.json())
      .then((data) => setMovies(data))
      .catch((error) => console.error("Lỗi khi lấy dữ liệu phim:", error));
  }, []);

  useEffect(() => {
    // Kiểm tra trạng thái đăng nhập từ localStorage
    const loggedInStatus = localStorage.getItem("isLoggedIn") === "true";
    const storedUsername = localStorage.getItem("username");
    setIsLoggedIn(loggedInStatus);
    setUsername(storedUsername || "");
  }, []);

  const handleLogout = () => {
    // Xử lý logout
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("username");
    setIsLoggedIn(false);
    setUsername("");
    navigate("/login");
  };

  const filteredMovies = movies.filter((movie) => movie.status === currentTab);

  return (
    <div>
      <Header
        isLoggedIn={isLoggedIn}
        username={username}
        onLogout={handleLogout}
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
    </div>
  );
}

export default Home;
