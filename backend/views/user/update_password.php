<?php
include_once '../../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Initialize response array
$response = ["status" => "error", "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']); // Lấy user_id từ frontend
    $new_password = trim($_POST['new_password']);

    if (empty($new_password) || empty($user_id)) {
        $response["message"] = "Dữ liệu không hợp lệ!";
        echo json_encode($response);
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $query = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        $response["status"] = "success";
        $response["message"] = "Mật khẩu đã được cập nhật!";
    } else {
        $response["message"] = "Không thể cập nhật mật khẩu. Vui lòng thử lại.";
    }

    $stmt->close();
} else {
    $response["message"] = "Phương thức không hợp lệ!";
}

// Return the response
echo json_encode($response);
?>
