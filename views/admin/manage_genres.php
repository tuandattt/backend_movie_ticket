<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}
// Lấy danh sách thể loại từ cơ sở dữ liệu
$query = "SELECT * FROM genres";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thể loại Phim</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Quản lý Thể loại Phim</h2>
        <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
        <a href="add_genre.php" class="add-btn">Thêm thể loại mới</a>
        <table border="1" class="genre-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên thể loại</th>
                    <th>Tùy chọn</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['genre_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['genre_name']); ?></td>
                            <td>
                                <a href="edit_genre.php?id=<?php echo $row['genre_id']; ?>" class="edit-btn">Chỉnh sửa</a>
                                <a href="delete_genre.php?id=<?php echo $row['genre_id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Không có thể loại nào được tìm thấy.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>