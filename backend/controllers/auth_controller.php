<?php
session_start();
include '../includes/config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra tài khoản trong cơ sở dữ liệu
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            if ($user['role'] === 'admin') {
                // Nếu là admin, chuyển hướng tới trang dashboard
                $_SESSION['admin'] = $user['username'];
                header("Location: ../views/admin/dashboard.php");
            } else {
                // Nếu là user, chuyển hướng tới trang home
                $_SESSION['user'] = $user['username'];
                header("Location: ../views/user/home.php");
            }
            exit();
        } else {
            // Sai mật khẩu
            $_SESSION['error'] = "Sai mật khẩu.";
            header("Location: ../views/user/login.php");
            exit();
        }
    } else {
        // Sai tên đăng nhập
        $_SESSION['error'] = "Tên đăng nhập không hợp lệ.";
        header("Location: ../views/user/login.php");
        exit();
    }
}
?>
