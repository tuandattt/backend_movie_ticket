<?php
session_start();
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: *'); // Cho phép mọi nguồn gốc truy cập
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

// Lấy phương thức yêu cầu
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Xử lý khi thanh toán từ frontend
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['payment_id'], $input['selected_seats'], $input['schedule_id']) || !is_array($input['selected_seats'])) {
        echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
        exit();
    }

    $payment_id = $input['payment_id'];
    $selected_seats = $input['selected_seats'];
    $schedule_id = $input['schedule_id'];

    $conn->begin_transaction();

    try {
        // Cập nhật trạng thái thanh toán thành 'confirmed'
        $update_payment_query = "UPDATE payments SET status = 'confirmed' WHERE payment_id = ? AND status = 'pending'";
        $stmt_update = $conn->prepare($update_payment_query);
        $stmt_update->bind_param("i", $payment_id);

        if (!$stmt_update->execute() || $stmt_update->affected_rows === 0) {
            throw new Exception("Lỗi khi cập nhật trạng thái thanh toán hoặc giao dịch không phải 'pending'.");
        }

        // Thêm các ghế vào bảng bookings
        $booking_query = "INSERT INTO bookings (payment_id, schedule_id, seat_number) VALUES (?, ?, ?)";
        $stmt_booking = $conn->prepare($booking_query);

        foreach ($selected_seats as $seat_number) {
            $stmt_booking->bind_param("iis", $payment_id, $schedule_id, $seat_number);
            if (!$stmt_booking->execute()) {
                throw new Exception("Lỗi khi thêm dữ liệu vào bảng bookings.");
            }
        }

        $conn->commit();

        echo json_encode(["status" => "success", "message" => "Thanh toán thành công và ghế đã được đặt."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} elseif ($method === 'GET') {
    // Xử lý khi quét mã QR
    $payment_id = $_GET['payment_id'] ?? null;

    if (!$payment_id) {
        echo json_encode(["status" => "error", "message" => "Payment ID không hợp lệ."]);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Kiểm tra trạng thái giao dịch
        $check_query = "SELECT status FROM payments WHERE payment_id = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("i", $payment_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy giao dịch.");
        }

        $payment = $result->fetch_assoc();
        if ($payment['status'] !== 'pending') {
            throw new Exception("Giao dịch không ở trạng thái 'pending'.");
        }

        // Cập nhật trạng thái thanh toán thành 'confirmed'
        $update_query = "UPDATE payments SET status = 'confirmed' WHERE payment_id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $payment_id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows === 0) {
            throw new Exception("Lỗi khi cập nhật trạng thái thanh toán.");
        }

        $conn->commit();

        echo "Thanh toán thành công! Cảm ơn bạn.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
}
?>
