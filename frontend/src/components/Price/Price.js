import React from "react";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";
import "./Price.css"; // Import CSS cho trang Price

const Price = () => {
  return (
    <>
      <Header />
      <div className="price-container">
        <div className="price-content">
          <img
            src= "img/ticket_prices.png"
            alt="Cinema Ticket Prices"
            className="ticket-image"
          />
        </div>
      </div>
      <Footer />
    </>
  );
};

export default Price;
