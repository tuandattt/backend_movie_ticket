<?php
session_start();

// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

include '../../includes/config.php';

// Lấy thông tin người dùng
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $user_query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();

    if (!$user) {
        echo "Người dùng không tồn tại.";
        exit();
    }
} else {
    echo "ID người dùng không hợp lệ.";
    exit();
}

// Cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = !empty($_POST['name']) ? $_POST['name'] : $user['name'];
    $age = !empty($_POST['age']) ? intval($_POST['age']) : $user['age'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $user['email'];
    $role = !empty($_POST['role']) ? $_POST['role'] : $user['role'];
    $membership_level = !empty($_POST['membership_level']) ? $_POST['membership_level'] : $user['membership_level'];
    $is_u23_confirmed = !empty($_POST['is_u23_confirmed']) ? $_POST['is_u23_confirmed'] : $user['is_u23_confirmed'];

    // Cập nhật thông tin vào cơ sở dữ liệu
    $update_query = "
        UPDATE users 
        SET name = ?, age = ?, email = ?, role = ?, membership_level = ?, is_u23_confirmed = ?
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sissssi", $name, $age, $email, $role, $membership_level, $is_u23_confirmed, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cập nhật thông tin người dùng thành công!";
        header("Location: manage_users.php");
        exit();
    } else {
        $error_message = "Không thể cập nhật thông tin. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Người dùng</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h1>Chỉnh sửa Người dùng</h1>
        <a href="manage_users.php" class="btn btn-back">Quay lại Quản lý Người dùng</a>

        <!-- Hiển thị lỗi nếu có -->
        <?php if (isset($error_message)): ?>
            <p class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- Form chỉnh sửa thông tin người dùng -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Họ và tên:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>

            <div class="form-group">
                <label for="age">Tuổi:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="form-group">
                <label for="role">Vai trò:</label>
                <select id="role" name="role">
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Người dùng</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                </select>
            </div>

            <div class="form-group">
                <label for="membership_level">Cấp bậc thành viên:</label>
                <select id="membership_level" name="membership_level">
                    <option value="bronze" <?php echo $user['membership_level'] === 'bronze' ? 'selected' : ''; ?>>Bronze</option>
                    <option value="silver" <?php echo $user['membership_level'] === 'silver' ? 'selected' : ''; ?>>Silver</option>
                    <option value="gold" <?php echo $user['membership_level'] === 'gold' ? 'selected' : ''; ?>>Gold</option>
                    <option value="platinum" <?php echo $user['membership_level'] === 'platinum' ? 'selected' : ''; ?>>Platinum</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_u23_confirmed">Xác nhận U23:</label>
                <select id="is_u23_confirmed" name="is_u23_confirmed">
                    <option value="yes" <?php echo $user['is_u23_confirmed'] === 'yes' ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="no" <?php echo $user['is_u23_confirmed'] === 'no' ? 'selected' : ''; ?>>Chưa xác nhận</option>
                </select>
            </div>

            <button type="submit" class="btn btn-save">Lưu thay đổi</button>
        </form>
    </div>
</body>
</html>
