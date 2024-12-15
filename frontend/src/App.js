import React from "react";
import { Routes, Route } from "react-router-dom";
import { Toaster } from "sonner";
import Home from "./components/Home/Home";
import LoginForm from "./components/Auth/LoginForm";
import SignUpForm from "./components/Auth/SignUpForm";
import ForgotPasswordForm from "./components/Auth/ForgotPasswordForm";
import NewPasswordForm from "./components/Auth/NewPasswordForm";
import BookingPage from "./components/BookingPage/BookingPage";
import Payment from "./components/Payment/Payment";
import ConfirmPayment from "./components/ComfirmPayment/ConfirmPayment";
import AuthProvider from "./context/AuthContext";
import "./App.css";

function App() {
  return (
    <AuthProvider>
      <div className="App">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<LoginForm />} />
          <Route path="/signup" element={<SignUpForm />} />
          <Route path="/forgot-password" element={<ForgotPasswordForm />} />
          <Route path="/new-password" element={<NewPasswordForm />} />
          <Route path="/booking" element={<BookingPage />} />
          <Route path="/payment" element={<Payment />} />
          <Route path="/confirm-payment" element={<ConfirmPayment />} />
        </Routes>
        <Toaster
          richColors
          position="top-left"
          toastOptions={{
            duration: 2000, // Mặc định 2 giây cho tất cả các toast
          }}
        />
      </div>
    </AuthProvider>
  );
}

export default App;
