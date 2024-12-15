<?php
session_start();
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: *'); // Cho phép mọi nguồn gốc truy cập
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy dữ liệu từ query string
    $payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : null;
    $schedule_id = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : null;
    $selected_seats = isset($_GET['selected_seats']) ? explode(',', $_GET['selected_seats']) : [];

    // Kiểm tra dữ liệu hợp lệ
    if (!$payment_id || !$schedule_id || empty($selected_seats)) {
        echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Kiểm tra trạng thái thanh toán
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

        echo json_encode([
            "status" => "success",
            "message" => "Thanh toán thành công và ghế đã được đặt.",
            "payment_id" => $payment_id,
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
}
?>
