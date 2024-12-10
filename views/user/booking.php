<?php
session_start();
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

$schedule_id = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : null;
if (!$schedule_id) {
    echo "Lỗi: Vui lòng chọn lịch chiếu hợp lệ.";
    exit();
}

$query = "
    SELECT s.schedule_id, s.show_date, s.show_time, s.theater, s.available_seats, s.seats,
           m.title AS movie_title, m.poster
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE s.schedule_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$schedule = $stmt->get_result()->fetch_assoc();

if (!$schedule) {
    echo "Lỗi: Lịch chiếu không tồn tại.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_number = trim($_POST['seat_number']);

    if (!is_numeric($seat_number) || intval($seat_number) < 1 || intval($seat_number) > intval($schedule['seats'])) {
        $error = "Số ghế không hợp lệ.";
    } else {
        $check_seat_query = "
            SELECT * FROM bookings 
            WHERE schedule_id = ? AND seat_number = ?
        ";
        $stmt = $conn->prepare($check_seat_query);
        $stmt->bind_param("is", $schedule_id, $seat_number);
        $stmt->execute();
        $seat_result = $stmt->get_result();

        if ($seat_result->num_rows > 0) {
            $error = "Ghế đã được đặt.";
        } else {
            $insert_booking = "
                INSERT INTO bookings (user_id, schedule_id, seat_number, booking_date, status, payment_status)
                VALUES (?, ?, ?, NOW(), 'pending', 'pending')
            ";
            $stmt = $conn->prepare($insert_booking);
            $stmt->bind_param("iis", $_SESSION['user_id'], $schedule_id, $seat_number);

            if ($stmt->execute()) {
                $success = "Đặt vé thành công! Vui lòng chuyển khoản đến tài khoản sau và chờ xác nhận: <br>
                            <strong>Ngân hàng:</strong> Vietcombank<br>
                            <strong>Số tài khoản:</strong> 1900561252<br>
                            <strong>Tên tài khoản:</strong> Nguyễn Văn A<br>
                            <br>Sau khi chuyển khoản, hãy chờ admin xác nhận!";
            } else {
                $error = "Không thể đặt vé. Vui lòng thử lại.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Vé - <?php echo htmlspecialchars($schedule['movie_title']); ?></title>
</head>
<body>
    <div class="container">
        <h1>Đặt Vé Xem Phim</h1>

        <section>
            <h2>Thông Tin Lịch Chiếu</h2>
            <img src="/movie_booking/assets/images/<?php echo htmlspecialchars($schedule['poster']); ?>" 
                 alt="<?php echo htmlspecialchars($schedule['movie_title']); ?>" width="200">
            <p><strong>Tên Phim:</strong> <?php echo htmlspecialchars($schedule['movie_title']); ?></p>
            <p><strong>Ngày Chiếu:</strong> <?php echo htmlspecialchars($schedule['show_date']); ?></p>
            <p><strong>Giờ Chiếu:</strong> <?php echo htmlspecialchars($schedule['show_time']); ?></p>
            <p><strong>Rạp:</strong> <?php echo htmlspecialchars($schedule['theater']); ?></p>
            <p><strong>Số Ghế:</strong> <?php echo htmlspecialchars($schedule['seats']); ?></p>
            <p><strong>Ghế Còn:</strong> <?php echo htmlspecialchars($schedule['available_seats']); ?></p>
        </section>

        <section>
            <h2>Chọn Ghế</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <form method="POST" action="">
                <label for="seat_number">Nhập Số Ghế (1-<?php echo intval($schedule['seats']); ?>):</label><br>
                <input type="number" id="seat_number" name="seat_number" 
                       min="1" max="<?php echo intval($schedule['seats']); ?>" required><br><br>
                <button type="submit">Xác Nhận Đặt Vé</button>
            </form>
        </section>

        <a href="/movie_booking/views/user/home.php">Quay lại Trang Chủ</a>
    </div>
</body>
</html>
