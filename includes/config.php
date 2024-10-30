<?php
// Cấu hình kết nối cơ sở dữ liệu MySQL
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "movie_booking";

// Tạo kết nối
$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Đặt mã hóa UTF-8 để xử lý tiếng Việt và các ký tự đặc biệt
$conn->set_charset("utf8mb4");
?>
