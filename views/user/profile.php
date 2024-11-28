<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy thông tin người dùng từ cơ sở dữ liệu
$userId = $_SESSION['user_id'];

$userQuery = "SELECT username, email, membership_level FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Lấy lịch sử đặt vé của người dùng
$bookingQuery = "
    SELECT b.booking_id, b.booking_date, b.seat_number, b.status, m.title, s.show_date, s.show_time
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.schedule_id
    JOIN movies m ON s.movie_id = m.movie_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
";
$stmt = $conn->prepare($bookingQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookingResult = $stmt->get_result();

// Lấy danh sách phim yêu thích của người dùng
$favoritesQuery = "
    SELECT m.movie_id, m.title, m.poster
    FROM reviews r
    JOIN movies m ON r.movie_id = m.movie_id
    WHERE r.user_id = ? AND r.rating >= 4
    ORDER BY r.review_date DESC
";
$stmt = $conn->prepare($favoritesQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$favoritesResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân</title>
</head>
<body>
    <h1>Hồ Sơ Cá Nhân</h1>

    <!-- Hiển thị thông tin cá nhân -->
    <section>
        <h2>Thông Tin Cá Nhân</h2>
        <p><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Cấp bậc thành viên:</strong> <?php echo htmlspecialchars($user['membership_level']); ?></p>
        <a href="edit_profile.php">Chỉnh sửa thông tin</a>
    </section>

    <!-- Hiển thị lịch sử đặt vé -->
    <section>
        <h2>Lịch Sử Đặt Vé</h2>
        <?php if ($bookingResult->num_rows > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID Đặt Vé</th>
                        <th>Ngày Đặt</th>
                        <th>Tên Phim</th>
                        <th>Ngày Chiếu</th>
                        <th>Giờ Chiếu</th>
                        <th>Số Ghế</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookingResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['title']); ?></td>
                            <td><?php echo htmlspecialchars($booking['show_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['show_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['seat_number']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Bạn chưa đặt vé nào.</p>
        <?php endif; ?>
    </section>

    <!-- Hiển thị danh sách phim yêu thích -->
    <section>
        <h2>Phim Yêu Thích</h2>
        <?php if ($favoritesResult->num_rows > 0): ?>
            <ul>
                <?php while ($favorite = $favoritesResult->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($favorite['title']); ?></strong><br>
                        <img src="../../assets/images/<?php echo htmlspecialchars($favorite['poster']); ?>" alt="<?php echo htmlspecialchars($favorite['title']); ?>" width="100">
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Bạn chưa có phim yêu thích nào.</p>
        <?php endif; ?>
    </section>
</body>
</html>
