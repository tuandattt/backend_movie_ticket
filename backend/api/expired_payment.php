<?php
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000'); // Địa chỉ của frontend
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Các phương thức được phép
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Các header được phép
header('Access-Control-Allow-Credentials: true'); // Cho phép gửi cookie
header('Content-Type: application/json'); // Đặt loại nội dung là JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_GET['payment_id'] ?? null;

    if (!$payment_id) {
        echo json_encode(["status" => "error", "message" => "Payment ID không hợp lệ."]);
        exit();
    }

    // Cập nhật trạng thái thành 'expired' và thêm thời gian hết hạn
    $update_query = "UPDATE payments SET status = 'expired', expires_at = NOW() WHERE payment_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Giao dịch đã bị hết hạn."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Giao dịch không thể hết hạn (không phải trạng thái 'pending')."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật trạng thái giao dịch."]);
    }
}
?>
