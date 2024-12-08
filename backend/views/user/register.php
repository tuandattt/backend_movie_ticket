<?php
include_once '../../includes/config.php';

// Cho phép mọi nguồn truy cập (bạn có thể giới hạn domain cụ thể nếu cần)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin người dùng từ form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra lỗi nhập liệu
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $response = [
            'status' => 'error',
            'message' => "Vui lòng điền đầy đủ các trường!"
        ];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = [
            'status' => 'error',
            'message' => "Địa chỉ email không hợp lệ!"
        ];
    } elseif ($password !== $confirm_password) {
        $response = [
            'status' => 'error',
            'message' => "Mật khẩu không khớp!"
        ];
    } else {
        // Kiểm tra xem username hoặc email đã tồn tại chưa
        $query_check = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query_check);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = [
                'status' => 'error',
                'message' => "Tên đăng nhập hoặc email đã tồn tại!"
            ];
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Thêm người dùng mới vào cơ sở dữ liệu
            $query_insert = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ."
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại!"
                ];
            }

            $stmt_insert->close();
        }
        $stmt->close();
    }

    // Trả về phản hồi dưới dạng JSON
    echo json_encode($response);
}
?>
