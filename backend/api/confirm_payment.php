<?php
session_start();
include_once '../includes/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy dữ liệu từ query string
    $payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : null;
    $schedule_id = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : null;
    $selected_seats = isset($_GET['selected_seats']) ? explode(',', $_GET['selected_seats']) : [];

    if (!$payment_id || !$schedule_id || empty($selected_seats)) {
        echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Kiểm tra trạng thái thanh toán
        $check_query = "SELECT status, user_id FROM payments WHERE payment_id = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("i", $payment_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy giao dịch.");
        }

        $payment = $result->fetch_assoc();
        $user_id = $payment['user_id'];

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

        // Thêm các ghế vào bảng bookings và tính giảm số ghế
        $booking_query = "INSERT INTO bookings (payment_id, schedule_id, seat_number) VALUES (?, ?, ?)";
        $update_seats_query = "UPDATE schedules SET available_seats = available_seats - ? WHERE schedule_id = ?";
        
        $stmt_booking = $conn->prepare($booking_query);
        $stmt_update_seats = $conn->prepare($update_seats_query);

        $total_seats_to_reduce = 0;

        foreach ($selected_seats as $seat_number) {
            $stmt_booking->bind_param("iis", $payment_id, $schedule_id, $seat_number);
            if (!$stmt_booking->execute()) {
                throw new Exception("Lỗi khi thêm dữ liệu vào bảng bookings.");
            }

            // Tính giảm số ghế (ghế đôi hoặc ghế đơn)
            $seats_to_reduce = strpos($seat_number, '-') !== false ? 2 : 1;
            $total_seats_to_reduce += $seats_to_reduce;
        }

        // Cập nhật số ghế trống trong bảng schedules
        $stmt_update_seats->bind_param("ii", $total_seats_to_reduce, $schedule_id);
        if (!$stmt_update_seats->execute()) {
            throw new Exception("Lỗi khi cập nhật số ghế trống.");
        }

        // Tính tổng amount trong bảng payments của user_id
        $sum_query = "SELECT SUM(amount) AS total_spent FROM payments WHERE user_id = ? AND status = 'confirmed'";
        $stmt_sum = $conn->prepare($sum_query);
        $stmt_sum->bind_param("i", $user_id);
        $stmt_sum->execute();
        $result_sum = $stmt_sum->get_result();
        $row_sum = $result_sum->fetch_assoc();
        $total_spent = $row_sum['total_spent'] ?? 0;

        // Cập nhật total_spent trong bảng users
        $update_total_spent_query = "UPDATE users SET total_spent = ? WHERE user_id = ?";
        $stmt_update_total = $conn->prepare($update_total_spent_query);
        $stmt_update_total->bind_param("ii", $total_spent, $user_id);
        $stmt_update_total->execute();

        // Commit giao dịch
        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Thanh toán thành công, ghế đã được đặt, số ghế trống đã giảm và tổng chi tiêu đã được cập nhật.",
            "payment_id" => $payment_id,
            "total_spent" => $total_spent
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
}
?>
