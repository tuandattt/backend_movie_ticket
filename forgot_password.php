<?php
session_start();
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'your_database';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        // Tạo mã đặt lại mật khẩu
        $reset_code = bin2hex(random_bytes(16));
        
        // Lưu mã đặt lại vào cơ sở dữ liệu
        $update_sql = "UPDATE users SET reset_code = '$reset_code' WHERE username = '$username'";
        if (mysqli_query($conn, $update_sql)) {
            $message = "Mã đặt lại mật khẩu của bạn là: " . $reset_code;
        } else {
            $message = "Có lỗi xảy ra. Vui lòng thử lại sau.";
        }
    } else {
        $message = "Không tìm thấy tài khoản với tên đăng nhập này.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 300px; margin: 0 auto; padding: 20px; }
        input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; }
        input[type="submit"] { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; }
        .message { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quên mật khẩu</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="submit" value="Đặt lại mật khẩu">
        </form>
        <?php if($message) { echo "<p class='message'>$message</p>"; } ?>
        <p><a href="login.php">Quay lại đăng nhập</a></p>
    </div>
</body>
</html>