<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy danh sách phim từ cơ sở dữ liệu
$query = "SELECT movies.movie_id, movies.title, movies.poster, GROUP_CONCAT(genres.genre_name SEPARATOR ', ') AS genre_names, 
                 movies.duration, movies.director, movies.actors, movies.trailer_link, movies.release_date, 
                 IFNULL(movies.status, 'N/A') AS status, movies.rating, movies.description
          FROM movies
          JOIN movie_genres ON movies.movie_id = movie_genres.movie_id
          JOIN genres ON movie_genres.genre_id = genres.genre_id
          GROUP BY movies.movie_id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phim</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Danh sách Phim</h2>
        <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
        <a href="add_movie.php" class="add-btn">Thêm phim mới</a>
        <table border="1" class="movie-table">
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Tên phim</th>
                    <th>Thể loại</th>
                    <th>Thời lượng</th>
                    <th>Đạo diễn</th>
                    <th>Diễn viên</th>
                    <th>Trailer</th>
                    <th>Ngày phát hành</th>
                    <th>Trạng thái</th>
                    <th>Rating</th>
                    <th>Mô tả</th>
                    <th>Tùy chọn</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../../assets/images/<?php echo htmlspecialchars($row['poster']); ?>" alt="Poster" width="100"></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['genre_names']); ?></td>
                            <td><?php echo htmlspecialchars($row['duration']); ?> phút</td>
                            <td><?php echo htmlspecialchars($row['director']); ?></td>
                            <td><?php echo htmlspecialchars($row['actors']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($row['trailer_link']); ?>" target="_blank">Xem trailer</a></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($row['release_date']))); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['rating']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="edit_movie.php?id=<?php echo $row['movie_id']; ?>" class="edit-btn">Chỉnh sửa</a>
                                <a href="delete_movie.php?id=<?php echo $row['movie_id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">Không có phim nào được tìm thấy.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
