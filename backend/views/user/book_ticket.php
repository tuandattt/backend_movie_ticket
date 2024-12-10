<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /movie_booking/views/user/login.php");
    exit();
}

// Get the schedule ID from the URL
$scheduleId = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : null;

if (!$scheduleId) {
    echo "Vui lòng chọn lịch chiếu hợp lệ.";
    exit();
}

// Get schedule details
$query = "
    SELECT s.schedule_id, m.title AS movie_title, s.show_date, s.show_time, s.theater, s.available_seats, s.seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE s.schedule_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $scheduleId);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();

if (!$schedule) {
    echo "Lịch chiếu không tồn tại.";
    exit();
}

// Handle seat booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seatNumber = trim($_POST['seat_number']);

    // Validate the seat number
    if ($seatNumber < 1 || $seatNumber > $schedule['seats']) {
        $error = "Số ghế không hợp lệ.";
    } else {
        // Check if the seat is already booked
        $checkSeatQuery = "
            SELECT * FROM bookings 
            WHERE schedule_id = ? AND seat_number = ?
        ";
        $stmt = $conn->prepare($checkSeatQuery);
        $stmt->bind_param("is", $scheduleId, $seatNumber);
        $stmt->execute();
        $seatResult = $stmt->get_result();

        if ($seatResult->num_rows > 0) {
            $error = "Ghế đã được đặt.";
        } else {
            // Book the seat
            $insertBooking = "
                INSERT INTO bookings (user_id, schedule_id, seat_number, booking_date, status)
                VALUES (?, ?, ?, NOW(), 'booked')
            ";
            $stmt = $conn->prepare($insertBooking);
            $stmt->bind_param("iis", $_SESSION['user_id'], $scheduleId, $seatNumber);

            if ($stmt->execute()) {
                // Update available seats
                $updateSeatsQuery = "UPDATE schedules SET available_seats = available_seats - 1 WHERE schedule_id = ?";
                $stmt = $conn->prepare($updateSeatsQuery);
                $stmt->bind_param("i", $scheduleId);
                $stmt->execute();

                $success = "Đặt vé thành công!";
            } else {
                $error = "Không thể đặt vé. Vui lòng thử lại.";
            }
        }
    }
}
?>