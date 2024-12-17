<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Kiểm tra session user_id
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

$userId = $_SESSION['user_id'];

// Kiểm tra xem file ảnh được gửi lên
if (isset($_FILES['avatar'])) {
    $file_name = time() . "_" . basename($_FILES['avatar']['name']); // Đặt tên file duy nhất
    $file_tmp = $_FILES['avatar']['tmp_name'];
    
    // Đường dẫn lưu ảnh trong thư mục public/uploads của frontend
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/web-project/frontend/public/uploads/";
    $file_path = $upload_dir . $file_name;

    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Di chuyển file vào thư mục
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Đường dẫn public để hiển thị trong React
        $avatar_url = "/uploads/" . $file_name;

        // Lưu đường dẫn ảnh mới vào database
        $query = "UPDATE users SET avatar = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $avatar_url, $userId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "avatar_url" => $avatar_url]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi cập nhật đường dẫn avatar"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi tải file ảnh"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Không có file nào được tải lên"]);
}
?>
