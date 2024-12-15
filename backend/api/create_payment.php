<?php
session_start();
include '../includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000"); // Địa chỉ frontend
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // Cho phép gửi cookie
header("Content-Type: application/json");

// Kiểm tra người dùng đã đăng nhập hay chưa
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Nếu không có user_id trong session, trả về lỗi
    echo json_encode(["status" => "error", "message" => "Người dùng chưa đăng nhập"]);
    exit();
}

// Xử lý yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra dữ liệu đầu vào
    if (!isset($input['amount'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Dữ liệu không hợp lệ"
        ]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $amount = $input['amount'];

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Thêm bản ghi vào bảng payments
        $payment_query = "INSERT INTO payments (user_id, amount, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($payment_query);
        $stmt->bind_param("id", $user_id, $amount);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi thêm dữ liệu vào bảng payments");
        }

        $payment_id = $stmt->insert_id; // Lấy ID của payment vừa tạo

        // Hoàn tất transaction
        $conn->commit();

        echo json_encode([
            "status" => "success",
            "payment_id" => $payment_id
        ]);
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollback();

        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Chỉ hỗ trợ phương thức POST"
    ]);
}
?>
