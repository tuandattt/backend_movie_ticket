<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'cinema_prj');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$message = ""; // Biến để lưu thông báo

// Kiểm tra xem form có được gửi không
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form và kiểm tra xem các giá trị có tồn tại không
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : null;
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $cccd = isset($_POST['cccd']) ? $_POST['cccd'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;

    // Kiểm tra xem các giá trị có rỗng không
    if (!$fullname || !$username || !$birthdate || !$cccd || !$password || !$email) {
        $message = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Kiểm tra tuổi (phải trên 16 tuổi)
        $birthDateTimestamp = strtotime($birthdate);
        $age = (date('Y') - date('Y', $birthDateTimestamp));
        if ($age < 16) {
            $message = "Bạn phải trên 16 tuổi để đăng ký.";
        }

        // Kiểm tra định dạng mật khẩu
        if (strlen($password) < 8 || strlen($password) > 16 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $message = "Mật khẩu phải có từ 8-16 ký tự, bao gồm chữ in hoa và số.";
        }

        // Kiểm tra định dạng CCCD (tối thiểu 8 ký tự)
        if (strlen($cccd) < 8 || !ctype_digit($cccd)) {
            $message = "CCCD phải có ít nhất 8 chữ số.";
        }

        // Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Định dạng email không hợp lệ.";
        }

        // Kiểm tra xem tên đăng nhập, CCCD, và email đã tồn tại hay chưa
        if (empty($message)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR cccd = ? OR email = ?");
            $stmt->bind_param("sss", $username, $cccd, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Tên đăng nhập, CCCD hoặc email đã tồn tại, không thể đăng ký.";
            } else {
                // Mã hóa mật khẩu
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Thêm dữ liệu vào cơ sở dữ liệu bằng prepared statement
                $stmt = $conn->prepare("INSERT INTO users (fullname, username, birthdate, cccd, password, email) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $fullname, $username, $birthdate, $cccd, $hashed_password, $email);

                if ($stmt->execute()) {
                    $message = "Đăng ký thành công!";
                } else {
                    $message = "Lỗi: " . $conn->error;
                }

                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký vé xem phim</title>
    <style>
        .message {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            color: red;
        }
    </style>
</head>
<body>
    <h2>Đăng ký</h2>
    <form action="" method="POST">
        <label for="fullname">Họ tên:</label><br>
        <input type="text" id="fullname" name="fullname" required><br><br>

        <label for="username">Tên đăng nhập:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="birthdate">Ngày sinh:</label><br>
        <input type="date" id="birthdate" name="birthdate" required><br><br>

        <label for="cccd">CCCD:</label><br>
        <input type="text" id="cccd" name="cccd" required><br><br>

        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="submit" value="Đăng ký">
    </form>

    <!-- Hiển thị thông báo -->
    <?php if (isset($message) && $message != ""): ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
</body>
</html>
