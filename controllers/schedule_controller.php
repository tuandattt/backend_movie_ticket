<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Không có quyền truy cập."]);
    exit();
}

// Detect if the request is asynchronous
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

// Handle GET requests for listing schedules
if (isset($_GET['action']) && $_GET['action'] === 'list_schedules') {
    $query = "
        SELECT s.schedule_id, m.title AS movie_title, s.show_date, s.show_time, s.theater, s.seats, s.available_seats
        FROM schedules s
        JOIN movies m ON s.movie_id = m.movie_id
        ORDER BY s.show_date ASC, s.show_time ASC
    ";
    $result = $conn->query($query);
    $schedules = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($schedules);
    exit();
}

// Handle POST requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Add a new schedule
    if ($action === 'add_schedule') {
        $movie_id = intval($_POST['movie_id']);
        $show_date = $_POST['show_date'];
        $show_time = $_POST['show_time'];
        $theater = $_POST['theater'];
        $seats = intval($_POST['seats']);

        // Check for schedule conflicts
        $conflictQuery = "
            SELECT COUNT(*) AS conflicts
            FROM schedules
            WHERE theater = ? AND show_date = ? AND show_time = ?
        ";
        $stmt = $conn->prepare($conflictQuery);
        $stmt->bind_param("sss", $theater, $show_date, $show_time);
        $stmt->execute();
        $conflictResult = $stmt->get_result()->fetch_assoc();

        if ($conflictResult['conflicts'] > 0) {
            if ($isAjax) {
                echo json_encode(["message" => "Lịch chiếu bị trùng trong cùng một rạp."]);
            } else {
                $_SESSION['error'] = "Lịch chiếu bị trùng trong cùng một rạp.";
                header("Location: /movie_booking/views/admin/manage_schedules.php");
            }
            exit();
        }

        // Add the new schedule
        $query = "INSERT INTO schedules (movie_id, show_date, show_time, theater, seats, available_seats) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssii", $movie_id, $show_date, $show_time, $theater, $seats, $seats);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($isAjax) {
                echo json_encode(["message" => "Lịch chiếu đã được thêm thành công."]);
            } else {
                $_SESSION['success'] = "Lịch chiếu đã được thêm thành công.";
                header("Location: /movie_booking/views/admin/manage_schedules.php");
            }
        } else {
            if ($isAjax) {
                echo json_encode(["message" => "Thêm lịch chiếu thất bại."]);
            } else {
                $_SESSION['error'] = "Thêm lịch chiếu thất bại.";
                header("Location: /movie_booking/views/admin/manage_schedules.php");
            }
        }
        exit();
    }

    // Delete a schedule
    if ($action === 'delete_schedule') {
        $schedule_id = intval($_POST['schedule_id']);

        $query = "DELETE FROM schedules WHERE schedule_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($isAjax) {
                echo json_encode(["message" => "Lịch chiếu đã được xóa."]);
            } else {
                $_SESSION['success'] = "Lịch chiếu đã được xóa.";
                header("Location: /movie_booking/views/admin/manage_schedules.php");
            }
        } else {
            if ($isAjax) {
                echo json_encode(["message" => "Xóa lịch chiếu thất bại."]);
            } else {
                $_SESSION['error'] = "Xóa lịch chiếu thất bại.";
                header("Location: /movie_booking/views/admin/manage_schedules.php");
            }
        }
        exit();
    }
}

// If no valid action was matched
http_response_code(400);
if ($isAjax) {
    echo json_encode(["message" => "Yêu cầu không hợp lệ."]);
} else {
    $_SESSION['error'] = "Yêu cầu không hợp lệ.";
    header("Location: /movie_booking/views/admin/manage_schedules.php");
}
exit();
