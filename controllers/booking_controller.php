<?php
// File: .../movie_booking/controllers/BookingController.php

session_start();
include_once '../../includes/config.php';

class BookingController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fetch schedules for a specific movie
    public function getSchedules($movieId)
    {
        $query = "
            SELECT * 
            FROM schedules 
            WHERE movie_id = ? AND available_seats > 0 
            ORDER BY show_date, show_time ASC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Book a seat
    public function bookSeat($userId, $scheduleId, $seatNumber)
    {
        // Begin transaction
        $this->conn->begin_transaction();

        try {
            // Check seat availability
            $availabilityQuery = "
                SELECT available_seats 
                FROM schedules 
                WHERE schedule_id = ? FOR UPDATE
            ";
            $stmt = $this->conn->prepare($availabilityQuery);
            $stmt->bind_param("i", $scheduleId);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedule = $result->fetch_assoc();

            if (!$schedule || $schedule['available_seats'] <= 0) {
                throw new Exception("Ghế đã hết chỗ!");
            }

            // Insert booking record
            $bookingQuery = "
                INSERT INTO bookings (user_id, schedule_id, seat_number, booking_date, status)
                VALUES (?, ?, ?, NOW(), 'booked')
            ";
            $stmt = $this->conn->prepare($bookingQuery);
            $stmt->bind_param("iis", $userId, $scheduleId, $seatNumber);
            $stmt->execute();

            // Update available seats
            $updateSeatsQuery = "
                UPDATE schedules 
                SET available_seats = available_seats - 1 
                WHERE schedule_id = ?
            ";
            $stmt = $this->conn->prepare($updateSeatsQuery);
            $stmt->bind_param("i", $scheduleId);
            $stmt->execute();

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $this->conn->rollback();
            return $e->getMessage();
        }
    }

    // Fetch user's booking history
    public function getBookingHistory($userId)
    {
        $query = "
            SELECT b.booking_id, b.booking_date, b.seat_number, b.status, m.title, s.show_date, s.show_time, s.theater
            FROM bookings b
            JOIN schedules s ON b.schedule_id = s.schedule_id
            JOIN movies m ON s.movie_id = m.movie_id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
}

// Initialize the controller
$bookingController = new BookingController($conn);

// Handle API requests or actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'book_seat') {
        $userId = $_SESSION['user_id'];
        $scheduleId = (int)$_POST['schedule_id'];
        $seatNumber = trim($_POST['seat_number']);

        $result = $bookingController->bookSeat($userId, $scheduleId, $seatNumber);
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Đặt vé thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }
}
?>
