<?php
session_start();
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000'); // Địa chỉ frontend
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Cho phép gửi cookie phiên
header("Content-Type: application/json");

// Kiểm tra phương thức GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy payment_id từ query parameters
    $payment_id = $_GET['payment_id'] ?? null;

    // Kiểm tra nếu payment_id không hợp lệ
    if (!$payment_id || !is_numeric($payment_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Payment ID không hợp lệ."
        ]);
        exit();
    }

    // Truy vấn trạng thái giao dịch từ bảng payments
    $query = "SELECT status FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu tìm thấy giao dịch
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "payment_status" => $payment['status'] // Trả về trạng thái giao dịch
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Không tìm thấy giao dịch."
        ]);
    }
} else {
    // Xử lý nếu không phải phương thức GET
    echo json_encode([
        "status" => "error",
        "message" => "Chỉ hỗ trợ phương thức GET."
    ]);
}
?>
