<?php
include_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Mật khẩu không khớp!";
    } else {
        // Kiểm tra email tồn tại trong cơ sở dữ liệu
        $query = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email hợp lệ, tiến hành cập nhật mật khẩu
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("ss", $hashed_password, $email);

            if ($stmt_update->execute()) {
                $success = "Mật khẩu đã được cập nhật!";
            } else {
                $error = "Không thể cập nhật mật khẩu, vui lòng thử lại.";
            }
        } else {
            $error = "Email không tồn tại trong hệ thống!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quên mật khẩu</title>
</head>
<body>
    <h2>Đặt lại mật khẩu</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST" action="">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Mật khẩu mới:</label>
        <input type="password" name="new_password" required><br>
        <label>Nhập lại mật khẩu mới:</label>
        <input type="password" name="confirm_password" required><br>
        <button type="submit">Cập nhật mật khẩu</button>
    </form>
    <br>
    <!-- Nút quay lại -->
    <button onclick="window.location.href='login.php'">Quay lại đăng nhập</button>
</body>
</html>
