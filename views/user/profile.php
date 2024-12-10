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

$userQuery = "
    SELECT username, name, age, email, avatar, role, membership_level, is_u23_confirmed
    FROM users 
    WHERE user_id = ?
";
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

// Lấy lịch sử đặt đồ ăn
$ordersQuery = "
    SELECT o.order_id, o.order_date, o.total_amount, o.status, 
           GROUP_CONCAT(CONCAT(oi.quantity, 'x ', s.name) SEPARATOR ', ') AS items
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN snacks s ON oi.item_id = s.snack_id
    WHERE o.user_id = ? AND oi.item_type = 'snack'
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($ordersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$ordersResult = $stmt->get_result();

// Xử lý các thông báo (success/error)
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Hồ Sơ Cá Nhân</h1>
    <a href="home.php">Quay lại Trang Chủ</a>

    <?php if ($successMessage): ?>
        <p style="color:green;"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <!-- Hiển thị thông tin cá nhân -->
    <section>
        <h2>Thông Tin Cá Nhân</h2>
        <?php if (!empty($user['avatar'])): ?>
            <img src="../../assets/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" width="100">
        <?php else: ?>
            <p>Chưa có ảnh đại diện.</p>
        <?php endif; ?>
        <p><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($user['name'] ?? 'Chưa cập nhật'); ?></p>
        <p><strong>Tuổi:</strong> <?php echo htmlspecialchars($user['age'] ?? 'Chưa cập nhật'); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Vai trò:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        <p><strong>Cấp bậc thành viên:</strong> <?php echo htmlspecialchars($user['membership_level']); ?></p>
        <p><strong>Trạng thái U23:</strong> <?php echo $user['is_u23_confirmed'] === 'yes' ? 'Đã xác nhận' : 'Chưa xác nhận'; ?></p>
        <a href="edit_profile.php">Chỉnh sửa thông tin</a>
    </section>

    <!-- Hiển thị lịch sử đặt vé -->
    <section>
        <h2>Lịch Sử Đặt Vé</h2>
        <?php if ($bookingResult->num_rows > 0): ?>
            <table>
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

    <!-- Hiển thị lịch sử đặt đồ ăn -->
    <section>
        <h2>Lịch Sử Đặt Đồ Ăn</h2>
        <?php if ($ordersResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Đơn Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Sản Phẩm</th>
                        <th>Tổng Tiền (VNĐ)</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['items']); ?></td>
                            <td><?php echo number_format($order['total_amount'], 0); ?> VNĐ</td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Bạn chưa đặt đồ ăn nào.</p>
        <?php endif; ?>
    </section>
</body>
</html>
