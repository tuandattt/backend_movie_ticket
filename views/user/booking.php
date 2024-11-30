<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy thông tin lịch chiếu từ URL
$scheduleId = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : null;

if (!$scheduleId) {
    echo "Vui lòng chọn lịch chiếu.";
    exit();
}

// Lấy thông tin lịch chiếu
$query = "
    SELECT s.schedule_id, s.show_date, s.show_time, s.theater, s.available_seats, m.title, m.poster
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

// Xử lý đặt vé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seatNumber = trim($_POST['seat_number']);

    // Kiểm tra ghế có khả dụng không
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
        // Đặt vé
        $insertBooking = "
            INSERT INTO bookings (user_id, schedule_id, seat_number, booking_date, status)
            VALUES (?, ?, ?, NOW(), 'booked')
        ";
        $stmt = $conn->prepare($insertBooking);
        $stmt->bind_param("iis", $_SESSION['user_id'], $scheduleId, $seatNumber);

        if ($stmt->execute()) {
            // Cập nhật số ghế khả dụng
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đặt Vé - <?php echo htmlspecialchars($schedule['title']); ?></title>
</head>
<body>
    <h1>Đặt Vé Xem Phim</h1>

    <!-- Hiển thị thông tin phim -->
    <section>
        <h2>Thông Tin Phim</h2>
        <img src="../../assets/images/<?php echo htmlspecialchars($schedule['poster']); ?>" alt="<?php echo htmlspecialchars($schedule['title']); ?>" width="200">
        <p><strong>Tên Phim:</strong> <?php echo htmlspecialchars($schedule['title']); ?></p>
        <p><strong>Ngày Chiếu:</strong> <?php echo htmlspecialchars($schedule['show_date']); ?></p>
        <p><strong>Giờ Chiếu:</strong> <?php echo htmlspecialchars($schedule['show_time']); ?></p>
        <p><strong>Rạp:</strong> <?php echo htmlspecialchars($schedule['theater']); ?></p>
        <p><strong>Ghế khả dụng:</strong> <?php echo htmlspecialchars($schedule['available_seats']); ?></p>
    </section>

    <!-- Hiển thị form đặt vé -->
    <section>
        <h2>Chọn Ghế</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <form method="POST" action="">
            <label for="seat_number">Nhập Số Ghế:</label><br>
            <input type="text" id="seat_number" name="seat_number" required><br><br>
            <button type="submit">Xác Nhận Đặt Vé</button>
        </form>
    </section>

    <a href="home.php">Quay lại Trang Chủ</a>
</body>
</html>
