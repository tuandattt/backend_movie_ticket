import React from "react";
import { Routes, Route } from "react-router-dom";
import { Toaster } from "sonner";
import Home from "./components/Home/Home";
import LoginForm from "./components/Auth/LoginForm";
import SignUpForm from "./components/Auth/SignUpForm";
import ForgotPasswordForm from "./components/Auth/ForgotPasswordForm";
import NewPasswordForm from "./components/Auth/NewPasswordForm";
import "./App.css";

function App() {
  return (
    <div className="App">
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/signup" element={<SignUpForm />} />
        <Route path="/forgot-password" element={<ForgotPasswordForm />} />
        <Route path="/new-password" element={<NewPasswordForm />} />
      </Routes>
      <Toaster
        richColors
        position="top-left"
        toastOptions={{
          duration: 2000, // Mặc định 2 giây cho tất cả các toast
        }}
      />
    </div>
  );
}

export default App;
