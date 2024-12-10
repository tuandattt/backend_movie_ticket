<?php
session_start();
include_once '../../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra username hoặc password có rỗng không
    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Tên đăng nhập và mật khẩu không được để trống!"]);
        exit();
    }

    // Truy vấn kiểm tra thông tin người dùng
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Kiểm tra thông tin người dùng
    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin vào session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['membership_level'] = $user['membership_level'];

        // Cập nhật thời gian đăng nhập cuối cùng
        $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $user['user_id']);
        $stmt_update->execute();

        // Gửi kết quả cho client
        $response = [
            "status" => "success",
            "username" => $user['username'],
            "role" => $user['role'],
            "redirect_url" => ($user['role'] === 'admin') ? 'http://localhost/web-project/backend/views/admin/dashboard.php' : 'http://localhost:3000/'
        ];
        echo json_encode($response);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Tên đăng nhập hoặc mật khẩu không đúng!"]);
    }
    exit();
}
?>
