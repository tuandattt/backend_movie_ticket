<?php
$host = "localhost";
$user = "root";           
$pass = "";                 
$dbname = "movie_booking"; 

// Tạo kết nối
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
