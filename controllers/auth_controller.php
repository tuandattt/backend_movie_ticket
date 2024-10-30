<?php
session_start();
include '../includes/config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra tài khoản Admin trong cơ sở dữ liệu
    $query = "SELECT * FROM users WHERE username = ? AND role = 'admin'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công, thiết lập session cho Admin
            $_SESSION['admin'] = $user['username'];
            header("Location: ../views/admin/dashboard.php");
            exit();
        } else {
            // Sai mật khẩu
            $_SESSION['error'] = "Sai mật khẩu.";
            header("Location: ../views/user/login.php");
            exit();
        }
    } else {
        // Sai tên đăng nhập hoặc tài khoản không có quyền Admin
        $_SESSION['error'] = "Tên đăng nhập hoặc quyền truy cập không hợp lệ.";
        header("Location: ../views/user/login.php");
        exit();
    }
}
