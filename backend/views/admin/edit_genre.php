<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy thông tin thể loại hiện tại từ cơ sở dữ liệu
$id = $_GET['id'];
$query = "SELECT * FROM genres WHERE genre_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$genre = $result->fetch_assoc();

if (isset($_POST['update_genre'])) {
    $genre_name = $_POST['genre_name'];

    $update_query = "UPDATE genres SET genre_name = ? WHERE genre_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $genre_name, $id);

    if ($update_stmt->execute()) {
        header("Location: manage_genres.php");
        exit();
    } else {
        echo "Đã xảy ra lỗi khi cập nhật thể loại: " . $update_stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Thể loại</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Chỉnh sửa Thể loại</h2>
        <form action="" method="POST">
            <label for="genre_name">Tên thể loại:</label>
            <input type="text" name="genre_name" value="<?php echo htmlspecialchars($genre['genre_name']); ?>" required>
            <br>
            <button type="submit" name="update_genre">Cập nhật thể loại</button>
        </form>
    </div>
</body>
</html>
