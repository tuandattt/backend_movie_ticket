<?php
session_start();
include '../includes/config.php';

// Kiểm tra nếu phương thức yêu cầu là POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra nếu form đăng nhập được gửi
    if (isset($_POST['login'])) {
        // Lấy và xử lý dữ liệu đầu vào
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Kiểm tra nếu username hoặc password trống
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ tên người dùng và mật khẩu.";
            header("Location: ../views/login.php");
            exit();
        }

        // Chuẩn bị truy vấn để tránh SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu tài khoản admin tồn tại
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Kiểm tra mật khẩu bằng cách xác minh mật khẩu đã mã hóa
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin'] = $user['username'];  // Lưu thông tin Admin vào session
                header("Location: ../views/admin/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Sai mật khẩu. Vui lòng thử lại.";
                header("Location: ../views/login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Tên người dùng không hợp lệ hoặc bạn không có quyền truy cập.";
            header("Location: ../views/login.php");
            exit();
        }

        // Đóng câu truy vấn
        $stmt->close();
    }
}

// Đóng kết nối
$conn->close();
?>
