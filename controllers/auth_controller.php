<?php
session_start();
include '../includes/config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra tài khoản Admin
    $query = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin'] = $user['username'];  // Lưu thông tin Admin vào session
            header("Location: ../views/admin/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Sai mật khẩu.";
            header("Location: ../views/user/login.php");
        }
    } else {
        $_SESSION['error'] = "Tài khoản không hợp lệ hoặc không có quyền truy cập.";
        header("Location: ../views/user/login.php");
    }
}
