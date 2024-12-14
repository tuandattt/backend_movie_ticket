<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu admin đã đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy danh sách booking đang chờ xác nhận
$query = "
    SELECT b.booking_id, b.seat_number, b.payment_status, b.status, u.username, s.show_date, s.show_time, m.title
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.schedule_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.payment_status = 'pending'
";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];

    if ($action === 'confirm') {
        $update_query = "UPDATE bookings SET payment_status = 'confirmed', status = 'booked' WHERE booking_id = ?";
    } else {
        $update_query = "UPDATE bookings SET payment_status = 'rejected', status = 'cancelled' WHERE booking_id = ?";
    }

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        header("Location: admin_bookings.php");
        exit();
    } else {
        echo "Cập nhật thất bại. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đặt Vé</title>
</head>
<body>
    <h1>Quản Lý Đặt Vé</h1>
    <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
    <table border="1">
        <thead>
            <tr>
                <th>Tên Người Dùng</th>
                <th>Phim</th>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Ghế</th>
                <th>Trạng Thái Thanh Toán</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['show_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['show_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                            <button type="submit" name="action" value="confirm">Chấp Nhận</button>
                            <button type="submit" name="action" value="reject">Từ Chối</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đặt Vé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f8ff;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007BFF;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f4f8ff;
        }

        tr:hover {
            background-color: #e6f7ff;
        }

        .actions button {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .actions button:first-child {
            background-color: #28a745;
            color: white;
        }

        .actions button:first-child:hover {
            background-color: #218838;
        }

        .actions button:last-child {
            background-color: #dc3545;
            color: white;
        }

        .actions button:last-child:hover {
            background-color: #c82333;
        }
    </style>
</head>

</html>
