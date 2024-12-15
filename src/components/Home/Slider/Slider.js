import React, { useState, useEffect } from "react";
import "./Slider.css";

const Slider = () => {
  const [currentIndex, setCurrentIndex] = useState(0);

  const sliderImages = [
    "img/slider1.jpg", // Đường dẫn ảnh 1
    "img/slider2.jpg", // Đường dẫn ảnh 2
    "img/slider3.jpg", // Đường dẫn ảnh 3
    "img/slider4.png", // Đường dẫn ảnh 4
  ];

  // Hàm chuyển đến slide tiếp theo
  const nextSlide = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex === sliderImages.length - 1 ? 0 : prevIndex + 1
    );
  };

  // Hàm chuyển đến slide trước
  const prevSlide = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex === 0 ? sliderImages.length - 1 : prevIndex - 1
    );
  };

  // Tự động chuyển slide sau 3 giây
  useEffect(() => {
    const interval = setInterval(() => {
      nextSlide();
    }, 3000);
    return () => clearInterval(interval); // Xóa interval khi component bị unmount
  }, []);

  return (
    <div className="slider-container">
      {/* Slider Wrapper */}
      <div
        className="slider-wrapper"
        style={{
          transform: `translateX(-${currentIndex * 100}%)`,
        }}
      >
        {sliderImages.map((image, index) => (
          <div className="slider-item" key={index}>
            <img
              src={image}
              alt={`Slide ${index + 1}`}
              className="slider-image"
            />
          </div>
        ))}
      </div>

      {/* Nút điều hướng */}
      <button className="slider-btn left" onClick={prevSlide}>
        &#8249;
      </button>
      <button className="slider-btn right" onClick={nextSlide}>
        &#8250;
      </button>

      {/* Chỉ báo */}
      <div className="slider-indicators">
        {sliderImages.map((_, index) => (
          <span
            key={index}
            className={`indicator ${currentIndex === index ? "active" : ""}`}
            onClick={() => setCurrentIndex(index)}
          ></span>
        ))}
      </div>
    </div>
  );
};

export default Slider;
