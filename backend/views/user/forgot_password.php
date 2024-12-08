<?php
include_once '../../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Initialize response array
$response = ["status" => "error", "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $email = trim($_POST['email']);

    // Check if email is provided
    if (empty($email)) {
        $response["message"] = "Vui lòng nhập email!";
        echo json_encode($response);
        exit();
    }

    // Check if email exists in the database
    $query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists
        $user = $result->fetch_assoc();
        $response["status"] = "success";
        $response["message"] = "Email hợp lệ. Vui lòng đặt lại mật khẩu!";
        $response["user_id"] = $user['user_id']; // Gửi user_id về frontend
    } else {
        $response["message"] = "Email không tồn tại trong hệ thống!";
    }

    $stmt->close();
} else {
    $response["message"] = "Phương thức không hợp lệ!";
}

// Return the response
echo json_encode($response);
?>
