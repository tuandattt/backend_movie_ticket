import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "./Account.css";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";

const Account = () => {
  const navigate = useNavigate();

  // State lưu thông tin người dùng
  const [userInfo, setUserInfo] = useState({
    avatar: "",
    name: "",
    email: "",
    age: "",
    phone_number: "",
  });

  const [avatarFile, setAvatarFile] = useState(null);

  // Fetch thông tin người dùng từ backend
  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await fetch(
          "http://localhost/web-project/backend/api/get_account_info.php",
          { credentials: "include" } // Gửi kèm cookie/session
        );
        const data = await response.json();

        if (data.status === "success") {
          setUserInfo({
            avatar: data.data.avatar || "",
            name: data.data.name || "",
            email: data.data.email || "",
            age: data.data.age || "",
            phone_number: data.data.phone_number || "",
          });
        } else {
          console.error(data.message);
          alert("Không thể lấy thông tin người dùng!");
        }
      } catch (error) {
        console.error("Lỗi khi lấy thông tin người dùng:", error);
      }
    };

    fetchUserData();
  }, []);

  // Xử lý khi chọn file ảnh đại diện
  const handleAvatarChange = (e) => {
    setAvatarFile(e.target.files[0]);
  };

  // Lưu ảnh đại diện mới
  const handleSaveAvatar = async () => {
    if (!avatarFile) {
      alert("Vui lòng chọn một file ảnh!");
      return;
    }

    const formData = new FormData();
    formData.append("avatar", avatarFile);

    try {
      const response = await fetch(
        "http://localhost/web-project/backend/api/upload_avatar.php",
        {
          method: "POST",
          credentials: "include",
          body: formData,
        }
      );
      const result = await response.json();

      if (result.status === "success") {
        alert("Ảnh đại diện đã được cập nhật!");
        setUserInfo({ ...userInfo, avatar: result.avatar_url });
      } else {
        alert(result.message || "Cập nhật ảnh thất bại!");
      }
    } catch (error) {
      console.error("Lỗi khi tải ảnh:", error);
    }
  };

  // Lưu thông tin người dùng
  const handleSaveInfo = async () => {
    try {
      const response = await fetch(
        "http://localhost/web-project/backend/api/update_account_info.php",
        {
          method: "POST",
          credentials: "include",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name: userInfo.name,
            age: userInfo.age,
            phone_number: userInfo.phone_number,
          }),
        }
      );
      const result = await response.json();

      if (result.status === "success") {
        alert(result.message);
      } else {
        alert("Cập nhật thông tin thất bại!");
      }
    } catch (error) {
      console.error("Lỗi khi cập nhật thông tin:", error);
    }
  };

  // Xử lý thay đổi trong input
  const handleChange = (e) => {
    const { name, value } = e.target;
    setUserInfo({ ...userInfo, [name]: value });
  };

  return (
    <>
      <div className="account-page-container">
        <Header />
        <div className="account-page-content-wrapper">
          <div className="account-container">
            {/* Div bao bọc với background white */}
            <div className="account-content-wrapper">
              {/* Phần tải ảnh đại diện */}
              <div className="image-upload">
                <img
                  src={
                    userInfo.avatar
                      ? `http://localhost:3000${userInfo.avatar}`
                      : "img/no-image.png"
                  }
                  alt="Avatar"
                  className="account-avatar-preview"
                />

                <div className="image-upload-controls">
                  <input
                    type="file"
                    accept="image/*"
                    className="account-avatar-input"
                    onChange={handleAvatarChange}
                  />
                  <button
                    className="account-avatar-button"
                    onClick={handleSaveAvatar}
                  >
                    Lưu Ảnh
                  </button>
                </div>
              </div>

              {/* Form cập nhật thông tin */}
              <div className="account-user-info-form">
                <div className="account-form-group">
                  <label>
                    <span className="account-required">*</span> Họ tên
                  </label>
                  <input
                    type="text"
                    name="name"
                    value={userInfo.name}
                    placeholder="Nhập họ tên"
                    className="account-input"
                    onChange={handleChange}
                  />
                </div>

                <div className="account-form-group">
                  <label>
                    <span className="account-required">*</span> Email
                  </label>
                  <input
                    type="email"
                    name="email"
                    value={userInfo.email}
                    className="account-input account-input-readonly"
                    readOnly
                  />
                </div>

                <div className="account-form-group">
                  <label>
                    <span className="account-required">*</span> Tuổi
                  </label>
                  <input
                    type="number"
                    name="age"
                    value={userInfo.age}
                    placeholder="Nhập tuổi"
                    className="account-input"
                    onChange={handleChange}
                  />
                </div>

                <div className="account-form-group">
                  <label>
                    <span className="account-required">*</span> Số điện thoại
                  </label>
                  <input
                    type="text"
                    name="phone_number"
                    value={userInfo.phone_number}
                    placeholder="Nhập số điện thoại"
                    className="account-input"
                    onChange={handleChange}
                  />
                </div>

                <button
                  className="account-save-info-btn"
                  onClick={handleSaveInfo}
                >
                  Cập Nhật Thông Tin
                </button>
              </div>
            </div>
          </div>
        </div>
        <Footer />
      </div>
    </>
  );
};

export default Account;
