<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Kiểm tra trạng thái yêu cầu U23 hiện tại
$requestQuery = "SELECT * FROM membership_requests WHERE user_id = ? ORDER BY submission_date DESC LIMIT 1";
$stmt = $conn->prepare($requestQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$requestResult = $stmt->get_result();
$currentRequest = $requestResult->fetch_assoc();

// Xử lý khi người dùng gửi yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membershipType = $_POST['membership_type'];
    $idImage = null;

    // Kiểm tra tệp tin được tải lên
    if (isset($_FILES['id_image']) && $_FILES['id_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['id_image']['tmp_name'];
        $imageName = time() . '_' . $_FILES['id_image']['name'];
        $imageUploadPath = "../../assets/membership_u23/" . $imageName;

        if (move_uploaded_file($imageTmpPath, $imageUploadPath)) {
            $idImage = $imageName;
        } else {
            $error = "Không thể tải lên ảnh.";
        }
    } else {
        $error = "Vui lòng tải lên ảnh hợp lệ.";
    }

    // Nếu ảnh tải lên thành công, lưu yêu cầu vào cơ sở dữ liệu
    if ($idImage) {
        $insertRequestQuery = "
            INSERT INTO membership_requests (user_id, membership_type, id_image, status)
            VALUES (?, ?, ?, 'pending')
        ";
        $stmt = $conn->prepare($insertRequestQuery);
        $stmt->bind_param("iss", $userId, $membershipType, $idImage);

        if ($stmt->execute()) {
            $success = "Yêu cầu đăng ký U23 đã được gửi thành công!";
        } else {
            $error = "Không thể gửi yêu cầu. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Thành Viên U23</title>
</head>
<body>
    <h1>Đăng Ký Thành Viên U23</h1>

    <!-- Hiển thị thông báo -->
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <!-- Hiển thị trạng thái yêu cầu hiện tại -->
    <?php if ($currentRequest): ?>
        <h2>Trạng Thái Yêu Cầu Hiện Tại</h2>
        <p><strong>Loại Yêu Cầu:</strong> <?php echo htmlspecialchars($currentRequest['membership_type']); ?></p>
        <p><strong>Ngày Gửi:</strong> <?php echo htmlspecialchars($currentRequest['submission_date']); ?></p>
        <p><strong>Trạng Thái:</strong> <?php echo htmlspecialchars($currentRequest['status']); ?></p>
        <?php if ($currentRequest['status'] === 'rejected'): ?>
            <p>Yêu cầu của bạn đã bị từ chối. Vui lòng gửi lại yêu cầu mới.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Form gửi yêu cầu -->
    <?php if (!$currentRequest || $currentRequest['status'] === 'rejected'): ?>
        <h2>Gửi Yêu Cầu Mới</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="membership_type">Loại Yêu Cầu:</label><br>
            <select id="membership_type" name="membership_type" required>
                <option value="CCCD">Căn Cước Công Dân</option>
                <option value="Student_ID">Mã Số Sinh Viên</option>
            </select><br><br>

            <label for="id_image">Ảnh CCCD/Mã Số Sinh Viên:</label><br>
            <input type="file" id="id_image" name="id_image" required><br><br>

            <button type="submit">Gửi Yêu Cầu</button>
        </form>
    <?php endif; ?>

    <a href="profile.php">Quay lại Hồ Sơ</a>
</body>
</html>