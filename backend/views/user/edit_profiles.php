<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy thông tin người dùng hiện tại
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

// Xử lý khi người dùng gửi form cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $membershipLevel = $_POST['membership_level'];
    $isU23Confirmed = $_POST['is_u23_confirmed'];

    // Xử lý upload ảnh đại diện
    $avatar = $user['avatar'];
$avatarUploadSuccess = true;

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $avatarTmpPath = $_FILES['avatar']['tmp_name'];
    $avatarName = time() . '_' . basename($_FILES['avatar']['name']);
    $avatarUploadPath = "../../assets/avatars/" . $avatarName;

    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!is_dir("../../assets/avatars/")) {
        mkdir("../../assets/avatars/", 0777, true);
    }

    // Di chuyển ảnh
    if (move_uploaded_file($avatarTmpPath, $avatarUploadPath)) {
        $avatar = $avatarName;
    } else {
        $avatarUploadSuccess = false;
        $error = "Không thể tải lên ảnh đại diện.";
    }
} elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    $avatarUploadSuccess = false;
    $error = "Lỗi tải lên ảnh: " . $_FILES['avatar']['error'];
}

    // Cập nhật thông tin người dùng
    if ($avatarUploadSuccess) {
        $updateQuery = "
            UPDATE users 
            SET username = ?, email = ?, name = ?, age = ?, avatar = ?, membership_level = ?, is_u23_confirmed = ?
            WHERE user_id = ?
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "sssssssi",
            $username,
            $email,
            $name,
            $age,
            $avatar,
            $membershipLevel,
            $isU23Confirmed,
            $userId
        );

        if ($stmt->execute()) {
            $success = "Thông tin cá nhân đã được cập nhật!";
        } else {
            $error = "Không thể cập nhật thông tin. Lỗi: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Hồ Sơ Cá Nhân</title>
</head>
<body>
    <h1>Chỉnh Sửa Hồ Sơ Cá Nhân</h1>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="username">Tên đăng nhập:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="name">Họ và tên:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"><br>

        <label for="age">Tuổi:</label><br>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>"><br>

        <label for="avatar">Ảnh đại diện:</label><br>
        <?php if (!empty($user['avatar'])): ?>
            <img src="../../assets/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" width="100"><br>
        <?php endif; ?>
        <input type="file" id="avatar" name="avatar"><br>

        <label for="membership_level">Cấp bậc thành viên:</label><br>
        <select id="membership_level" name="membership_level">
            <option value="bronze" <?php echo $user['membership_level'] === 'bronze' ? 'selected' : ''; ?>>Bronze</option>
            <option value="silver" <?php echo $user['membership_level'] === 'silver' ? 'selected' : ''; ?>>Silver</option>
            <option value="gold" <?php echo $user['membership_level'] === 'gold' ? 'selected' : ''; ?>>Gold</option>
            <option value="platinum" <?php echo $user['membership_level'] === 'platinum' ? 'selected' : ''; ?>>Platinum</option>
        </select><br>

        <label for="is_u23_confirmed">Trạng thái U23:</label><br>
        <select id="is_u23_confirmed" name="is_u23_confirmed">
            <option value="yes" <?php echo $user['is_u23_confirmed'] === 'yes' ? 'selected' : ''; ?>>Đã xác nhận</option>
            <option value="no" <?php echo $user['is_u23_confirmed'] === 'no' ? 'selected' : ''; ?>>Chưa xác nhận</option>
        </select><br>

        <button type="submit">Cập nhật</button>
    </form>

    <a href="profile.php">Quay lại Hồ Sơ</a>
</body>
</html>