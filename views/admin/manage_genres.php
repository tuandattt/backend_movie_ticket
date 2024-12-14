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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê & Báo cáo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8ff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            color: #007BFF;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .stat-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            background-color: #f9fbff;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #007BFF;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin: 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .stat-cards {
                flex-direction: column;
            }
        }
    </style>
</head>

</html>
