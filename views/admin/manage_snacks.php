<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

include '../../includes/config.php';

// Xử lý xác nhận hoặc từ chối đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action']; // 'confirm' hoặc 'reject'

    if ($action === 'confirm') {
        $status = 'completed';
    } elseif ($action === 'reject') {
        $status = 'cancelled';
    }

    $updateOrderQuery = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateOrderQuery);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        if ($action === 'confirm') {
            // Cập nhật tổng số tiền mà người dùng đã tiêu
            $getOrderDetailsQuery = "
                SELECT o.user_id, o.total_amount
                FROM orders o
                WHERE o.order_id = ?
            ";
            $stmt = $conn->prepare($getOrderDetailsQuery);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $orderDetails = $stmt->get_result()->fetch_assoc();

            if ($orderDetails) {
                $user_id = $orderDetails['user_id'];
                $total_amount = $orderDetails['total_amount'];

                $updateUserSpentQuery = "
                    UPDATE users 
                    SET total_spent = total_spent + ?
                    WHERE user_id = ?
                ";
                $stmt = $conn->prepare($updateUserSpentQuery);
                $stmt->bind_param("di", $total_amount, $user_id);
                $stmt->execute();
            }
        }

        $success_message = ($action === 'confirm') ? "Đơn hàng đã được xác nhận." : "Đơn hàng đã bị từ chối.";
    } else {
        $error_message = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.";
    }
}

// Lấy danh sách đơn hàng
$ordersQuery = "
    SELECT o.order_id, o.user_id, u.username, o.order_date, o.total_amount, o.status
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.status = 'pending'
    ORDER BY o.order_date DESC
";
$ordersResult = $conn->query($ordersQuery);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng Bổ sung</title>
</head>
<body>
    <h1>Quản lý Đơn hàng Bổ sung</h1>

    <!-- Hiển thị thông báo -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Danh sách đơn hàng -->
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID Đơn hàng</th>
                <th>Người dùng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền (VNĐ)</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $ordersResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo number_format($order['total_amount'], 0); ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" name="action" value="confirm">Xác nhận</button>
                            <button type="submit" name="action" value="reject">Từ chối</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php">Quay lại Dashboard</a>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn hàng Bổ sung</title>
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
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            margin: 10px 0;
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
            color: #fff;
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
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
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

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

</html>
