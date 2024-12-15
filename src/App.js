import React from "react";
import { Routes, Route } from "react-router-dom";
import { Toaster } from "sonner";
import Home from "./components/Home/Home";
import LoginForm from "./components/Auth/LoginForm";
import SignUpForm from "./components/Auth/SignUpForm";
import ForgotPasswordForm from "./components/Auth/ForgotPasswordForm";
import NewPasswordForm from "./components/Auth/NewPasswordForm";
<<<<<<< HEAD
import Account from "./components/Account/account";
import Price from "./components/Price/Price";
import MovieDetail from "./components/MovieDetails/MovieDetails"
=======
import BookingPage from "./components/BookingPage/BookingPage";
import AuthProvider from "./context/AuthContext";
>>>>>>> 408a5811cc1bdb3635d10bc8a85dfb6abb215592
import "./App.css";

function App() {
  return (
<<<<<<< HEAD
    <div className="App">
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/signup" element={<SignUpForm />} />
        <Route path="/forgot-password" element={<ForgotPasswordForm />} />
        <Route path="/new-password" element={<NewPasswordForm />} />
        <Route path="/account" element={<Account />} />
        <Route path="/pricing" element={<Price />} />
        <Route path="/movie/:movieId" element={<MovieDetail />} />
      </Routes>
      <Toaster
        richColors
        position="top-left"
        toastOptions={{
          duration: 2000, // Mặc định 2 giây cho tất cả các toast
        }}
      />
    </div>
=======
    <AuthProvider>
      <div className="App">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<LoginForm />} />
          <Route path="/signup" element={<SignUpForm />} />
          <Route path="/forgot-password" element={<ForgotPasswordForm />} />
          <Route path="/new-password" element={<NewPasswordForm />} />
          <Route path="/booking" element={<BookingPage />} />
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
>>>>>>> 408a5811cc1bdb3635d10bc8a85dfb6abb215592
  );
}

export default App;
