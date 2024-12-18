import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "./Account.css";
import Header from "../Home/Header/Header";
import Footer from "../Home/Footer/Footer";

const Account = () => {
  const navigate = useNavigate();

  const [membershipInfo, setMembershipInfo] = useState(null);
  const [activeTab, setActiveTab] = useState("info"); // State để quản lý tab
  const [cinemaJourney, setCinemaJourney] = useState([]);

  const formatTime = (time) => {
    const [hours, minutes] = time.split(":"); // Lấy giờ và phút
    return `${hours}:${minutes}`;
  };

  // Định dạng ngày chiếu (dd/mm/yyyy)
  const formatDate = (date) => {
    const [year, month, day] = date.split("-"); // Tách chuỗi yyyy-mm-dd
    return `${day}/${month}/${year}`; // Trả về dd/mm/yyyy
  };

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

  const fetchMembershipInfo = async () => {
    try {
      const response = await fetch(
        "http://localhost/web-project/backend/api/get_membership_info.php",
        { credentials: "include" }
      );
      const result = await response.json();

      if (result.status === "success") {
        setMembershipInfo(result.data);
      } else {
        alert("Không thể lấy thông tin thẻ thành viên!");
      }
    } catch (error) {
      console.error("Lỗi khi lấy thông tin thẻ thành viên:", error);
    }
  };

  const fetchCinemaJourney = async () => {
    try {
      const response = await fetch(
        "http://localhost/web-project/backend/api/get_cinema_journey.php",
        { credentials: "include" }
      );
      const result = await response.json();

      if (result.status === "success") {
        setCinemaJourney(result.data);
      } else {
        alert("Không thể lấy thông tin hành trình điện ảnh!");
      }
    } catch (error) {
      console.error("Lỗi khi lấy hành trình điện ảnh:", error);
    }
  };

  const handleTabClick = (tab) => {
    setActiveTab(tab);
    if (tab === "membership") {
      fetchMembershipInfo();
    }
    if (tab === "journey") {
      fetchCinemaJourney();
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
            <div className="account-nav-tabs">
              <div
                className={`account-tab ${
                  activeTab === "info" ? "active" : ""
                }`}
                onClick={() => handleTabClick("info")}
              >
                THÔNG TIN TÀI KHOẢN
              </div>
              <div
                className={`account-tab ${
                  activeTab === "membership" ? "active" : ""
                }`}
                onClick={() => handleTabClick("membership")}
              >
                THẺ THÀNH VIÊN
              </div>
              <div
                className={`account-tab ${
                  activeTab === "journey" ? "active" : ""
                }`}
                onClick={() => handleTabClick("journey")}
              >
                HÀNH TRÌNH ĐIỆN ẢNH
              </div>
            </div>
            <div className="account-content-wrapper">
              {activeTab === "info" && (
                <>
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
                        <span className="account-required">*</span> Số điện
                        thoại
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
                </>
              )}

              {activeTab === "membership" && membershipInfo && (
                <table className="membership-info-table">
                  <thead>
                    <tr>
                      <th>SỐ THẺ</th>
                      <th>HẠNG THẺ</th>
                      <th>NGÀY KÍCH HOẠT</th>
                      <th>TỔNG CHI TIÊU</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{membershipInfo.user_id}</td>
                      <td>{membershipInfo.membership_level}</td>
                      <td>{formatDate(membershipInfo.activated_date)}</td>
                      <td>
                        {parseFloat(membershipInfo.total_spent) % 1 === 0
                          ? parseInt(
                              membershipInfo.total_spent
                            ).toLocaleString() + " VND"
                          : parseFloat(
                              membershipInfo.total_spent
                            ).toLocaleString() + " VND"}
                      </td>
                    </tr>
                  </tbody>
                </table>
              )}

              {activeTab === "journey" && (
                <table className="cinema-journey-table">
                  <thead>
                    <tr>
                      <th>MÃ HÓA ĐƠN</th>
                      <th>PHIM</th>
                      <th>RẠP CHIẾU</th>
                      <th>SUẤT CHIẾU</th>
                      <th>NGÀY CHIẾU</th>
                      <th>GHẾ ĐÃ ĐẶT</th>
                      <th>ĐÃ THANH TOÁN</th>
                      <th>NGÀY ĐẶT</th>
                    </tr>
                  </thead>
                  <tbody>
                    {cinemaJourney.length > 0 ? (
                      cinemaJourney.map((item) => (
                        <tr key={item.order_id}>
                          <td>{item.order_id}</td>
                          <td>{item.movie_title}</td>
                          <td>{item.theater}</td>
                          <td>{formatTime(item.showtime)}</td>
                          <td>{formatDate(item.show_date)}</td>
                          <td>{item.booked_seats}</td>
                          <td>
                            {parseFloat(item.amount).toLocaleString()} VND
                          </td>
                          <td>{formatDate(item.booking_date)}</td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan="8">
                          Không có dữ liệu hành trình điện ảnh.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              )}
            </div>
          </div>
        </div>
        <Footer />
      </div>
    </>
  );
};

export default Account;
