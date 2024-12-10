<?php
session_start();
include '../../includes/config.php';



// Lấy danh sách các thành viên
$query = "
    SELECT user_id, username, name, email, membership_level, is_u23_confirmed, created_at
    FROM users
    WHERE role = 'user'
    ORDER BY created_at DESC
";
$result = $conn->query($query);

// Xử lý nâng cấp cấp bậc thành viên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_membership'])) {
    $userId = (int)$_POST['user_id'];
    $newMembershipLevel = $_POST['membership_level'];

    $updateQuery = "UPDATE users SET membership_level = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newMembershipLevel, $userId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Đã cập nhật cấp bậc thành viên thành công!";
    } else {
        $_SESSION['error_message'] = "Không thể cập nhật cấp bậc thành viên.";
    }

    header("Location: manage_memberships.php");
    exit();
}

// Thông báo
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Cấp Bậc Thành Viên</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Quản Lý Cấp Bậc Thành Viên</h1>

    <!-- Hiển thị thông báo -->
    <?php if ($successMessage): ?>
        <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Đăng Nhập</th>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>Cấp Bậc Thành Viên</th>
                <th>Trạng Thái U23</th>
                <th>Ngày Tạo</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['name'] ?? 'Chưa cập nhật'); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['membership_level']); ?></td>
                        <td><?php echo $user['is_u23_confirmed'] === 'yes' ? 'Đã xác nhận' : 'Chưa xác nhận'; ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <select name="membership_level" required>
                                    <option value="bronze" <?php echo $user['membership_level'] === 'bronze' ? 'selected' : ''; ?>>Bronze</option>
                                    <option value="silver" <?php echo $user['membership_level'] === 'silver' ? 'selected' : ''; ?>>Silver</option>
                                    <option value="gold" <?php echo $user['membership_level'] === 'gold' ? 'selected' : ''; ?>>Gold</option>
                                    <option value="platinum" <?php echo $user['membership_level'] === 'platinum' ? 'selected' : ''; ?>>Platinum</option>
                                </select>
                                <button type="submit" name="update_membership">Cập Nhật</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Không có thành viên nào để hiển thị.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>