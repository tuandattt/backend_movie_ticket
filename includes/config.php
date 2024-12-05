<?php
$db_host = '127.0.0.1';
$db_name = 'movie_booking';
$db_user = 'root';
$db_pass = ''; // Thay bằng mật khẩu của bạn nếu có.

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>