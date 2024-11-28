<?php
session_start();
include_once '../../includes/config.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    // Xác minh thông tin session với cơ sở dữ liệu
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Chuyển hướng dựa trên vai trò
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        // Xóa session nếu thông tin không hợp lệ
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra username hoặc password có rỗng không
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Tên đăng nhập và mật khẩu không được để trống!";
        header("Location: login.php");
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

        // Chuyển hướng dựa trên vai trò
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        // Thông báo lỗi nếu đăng nhập không thành công
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-login:hover {
            background-color: #45a049;
        }
        .extra-links {
            margin-top: 15px;
        }
        .extra-links a {
            text-decoration: none;
            color: #007BFF;
            margin: 0 5px;
        }
        .extra-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);  // Xóa thông báo lỗi sau khi hiển thị
        }
        ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn-login">Đăng nhập</button>
        </form>

        <div class="extra-links">
            <a href="register.php">Đăng ký</a>
            <a href="forgot_password.php">Quên mật khẩu?</a>
        </div>
    </div>
</body>
</html>
