<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy danh sách sản phẩm bổ sung
$snacksQuery = "SELECT * FROM snacks";
$snacksResult = $conn->query($snacksQuery);

// Lấy chương trình khuyến mãi hiện tại
$promotionsQuery = "
    SELECT * FROM promotions 
    WHERE applies_to IN ('snack', 'combo') 
      AND start_date <= CURDATE() 
      AND end_date >= CURDATE()
";
$promotionsResult = $conn->query($promotionsQuery);
$promotion = $promotionsResult->fetch_assoc();

// Xử lý khi người dùng mua sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $snackIds = $_POST['snack_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (!empty($snackIds) && !empty($quantities)) {
        $userId = $_SESSION['user_id'];
        $totalAmount = 0;

        // Tạo đơn hàng mới
        $insertOrderQuery = "INSERT INTO orders (user_id, order_date, status, total_amount) VALUES (?, NOW(), 'pending', 0)";
        $stmt = $conn->prepare($insertOrderQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        // Lưu từng sản phẩm vào `order_items`
        foreach ($snackIds as $index => $snackId) {
            $quantity = (int)$quantities[$index];
            if ($quantity > 0) {
                // Lấy thông tin sản phẩm
                $snackQuery = "SELECT * FROM snacks WHERE snack_id = ?";
                $stmt = $conn->prepare($snackQuery);
                $stmt->bind_param("i", $snackId);
                $stmt->execute();
                $snack = $stmt->get_result()->fetch_assoc();

                if ($snack) {
                    $price = $snack['price'];
                    $subtotal = $price * $quantity;

                    // Áp dụng khuyến mãi (nếu có)
                    if ($promotion) {
                        $discount = $subtotal * ($promotion['discount_percent'] / 100);
                        $subtotal -= $discount;
                    }

                    $totalAmount += $subtotal;

                    // Thêm sản phẩm vào `order_items`
                    $insertItemQuery = "
                        INSERT INTO order_items (order_id, item_id, item_type, quantity, price)
                        VALUES (?, ?, 'snack', ?, ?)
                    ";
                    $stmt = $conn->prepare($insertItemQuery);
                    $stmt->bind_param("iiid", $orderId, $snackId, $quantity, $subtotal);
                    $stmt->execute();
                }
            }
        }

        // Cập nhật tổng tiền đơn hàng
        $updateOrderQuery = "UPDATE orders SET total_amount = ? WHERE order_id = ?";
        $stmt = $conn->prepare($updateOrderQuery);
        $stmt->bind_param("di", $totalAmount, $orderId);
        $stmt->execute();

        $success = "Đặt hàng thành công! Tổng số tiền: " . number_format($totalAmount, 0) . " VND.<br>
        Vui lòng chuyển khoản vào tài khoản: <strong>1900561252</strong> Vietcombank - Nguyễn Văn A.<br>
        Chúng tôi sẽ xác nhận đơn hàng của bạn trong thời gian sớm nhất.";
    } else {
        $error = "Vui lòng chọn ít nhất một sản phẩm và số lượng hợp lệ.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mua Sản Phẩm Bổ Sung</title>
</head>
<body>
    <h1>Mua Sản Phẩm Bổ Sung</h1>

    <!-- Hiển thị thông báo -->
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <!-- Hiển thị khuyến mãi -->
    <?php if ($promotion): ?>
        <h2>Khuyến Mãi Đặc Biệt</h2>
        <p><strong><?php echo htmlspecialchars($promotion['name']); ?>:</strong> Giảm <?php echo htmlspecialchars($promotion['discount_percent']); ?>% áp dụng cho <?php echo htmlspecialchars($promotion['applies_to']); ?></p>
    <?php endif; ?>

    <!-- Hiển thị danh sách sản phẩm -->
    <form method="POST" action="">
        <h2>Danh Sách Sản Phẩm</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Sản Phẩm</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Số Lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($snack = $snacksResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($snack['name']); ?></td>
                        <td><?php echo htmlspecialchars($snack['type']); ?></td>
                        <td><?php echo number_format($snack['price'], 0); ?> VND</td>
                        <td>
                            <input type="hidden" name="snack_id[]" value="<?php echo $snack['snack_id']; ?>">
                            <input type="number" name="quantity[]" min="0" value="0">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <button type="submit">Xác Nhận Mua</button>
    </form>

    <a href="home.php">Quay lại Trang Chủ</a>
</body>
</html>
