<?php
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_GET['payment_id'] ?? null;

    if (!$payment_id) {
        echo json_encode(["status" => "error", "message" => "Payment ID không hợp lệ."]);
        exit();
    }

    // Kiểm tra nếu trạng thái là 'pending' thì mới cho phép xóa
    $check_query = "SELECT status FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();

    if ($payment['status'] !== 'pending') {
        echo json_encode(["status" => "error", "message" => "Chỉ có thể hủy giao dịch ở trạng thái 'pending'."]);
        exit();
    }

    // Xóa giao dịch từ bảng payments
    $delete_query = "DELETE FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Giao dịch đã bị hủy."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi hủy giao dịch."]);
    }
}
?>
