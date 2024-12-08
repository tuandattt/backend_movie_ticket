<?php
session_start();
include '../../includes/config.php';
include '../../includes/phpqrcode/qrlib.php'; // Thư viện tạo mã QR

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy ID đặt vé từ URL
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
if ($booking_id <= 0) {
    echo "ID đặt vé không hợp lệ.";
    exit();
}

// Lấy thông tin đặt vé từ cơ sở dữ liệu
$bookingQuery = "
    SELECT b.*, m.title AS movie_title, s.show_date, s.show_time, s.theater
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.schedule_id
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE b.booking_id = ? AND b.user_id = ?
";
$stmt = $conn->prepare($bookingQuery);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    echo "Không tìm thấy thông tin vé.";
    exit();
}

// Tạo mã QR nếu chưa tồn tại
if (empty($booking['qr_code_path'])) {
    $qrText = "Vé xem phim\n" .
              "Phim: " . $booking['movie_title'] . "\n" .
              "Rạp: " . $booking['theater'] . "\n" .
              "Ngày: " . $booking['show_date'] . "\n" .
              "Giờ: " . $booking['show_time'] . "\n" .
              "Ghế: " . $booking['seat_number'];
              
    $qrFile = "../../assets/qrcodes/booking_" . $booking_id . ".png";
    QRcode::png($qrText, $qrFile);

    // Cập nhật đường dẫn mã QR vào cơ sở dữ liệu
    $updateQuery = "UPDATE bookings SET qr_code_path = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $qrFile, $booking_id);
    $stmt->execute();
} else {
    $qrFile = $booking['qr_code_path'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Vé</title>
</head>
<body>
    <h1>Thông Tin Vé</h1>
    <p><strong>Phim:</strong> <?php echo htmlspecialchars($booking['movie_title']); ?></p>
    <p><strong>Rạp:</strong> <?php echo htmlspecialchars($booking['theater']); ?></p>
    <p><strong>Ngày:</strong> <?php echo htmlspecialchars($booking['show_date']); ?></p>
    <p><strong>Giờ:</strong> <?php echo htmlspecialchars($booking['show_time']); ?></p>
    <p><strong>Ghế:</strong> <?php echo htmlspecialchars($booking['seat_number']); ?></p>

    <h2>Mã QR</h2>
    <p>Quét mã QR dưới đây để kiểm tra vé:</p>
    <img src="<?php echo $qrFile; ?>" alt="Mã QR của bạn">

    <p><a href="profile.php">Quay lại hồ sơ cá nhân</a></p>
</body>
</html>
