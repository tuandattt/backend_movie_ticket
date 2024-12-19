<?php
session_start();
include '../../includes/config.php';

// Lấy danh sách phim từ cơ sở dữ liệu với nhiều thể loại
$query = "SELECT movies.movie_id, movies.title, movies.poster, 
                 GROUP_CONCAT(genres.genre_name SEPARATOR ', ') AS genre_names, 
                 movies.duration, movies.director, movies.actors, movies.trailer_link, 
                 movies.release_date, IFNULL(movies.status, 'N/A') AS status, 
                 movies.description
          FROM movies
          LEFT JOIN movie_genres ON movies.movie_id = movie_genres.movie_id
          LEFT JOIN genres ON movie_genres.genre_id = genres.genre_id
          GROUP BY movies.movie_id";
$result = $conn->query($query);

if (!$result) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phim</title>
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

        h2 {
            color: #007BFF;
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

        .add-btn, .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 12px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .add-btn:hover, .back-btn:hover {
            background-color: #0056b3;
        }

        .edit-btn {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        img {
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function updateStatus(selectElement, movieId) {
            var newStatus = selectElement.value;

            // Gửi yêu cầu AJAX để cập nhật trạng thái
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Trạng thái đã được cập nhật thành công.");
                } else if (xhr.readyState === 4) {
                    alert("Đã xảy ra lỗi khi cập nhật trạng thái.");
                }
            };
            xhr.send("movie_id=" + movieId + "&status=" + newStatus);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Danh sách Phim</h2>
        <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
        <a href="add_movie.php" class="add-btn">Thêm phim mới</a>
        <table>
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
                            <td>
                                <select name="status" onchange="updateStatus(this, <?php echo $row['movie_id']; ?>)">
                                    <option value="coming_soon" <?php if ($row['status'] === 'coming_soon') echo 'selected'; ?>>Coming Soon</option>
                                    <option value="now_showing" <?php if ($row['status'] === 'now_showing') echo 'selected'; ?>>Now Showing</option>
                                    <option value="stopped" <?php if ($row['status'] === 'stopped') echo 'selected'; ?>>Stopped</option>
                                </select>
                            </td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="edit_movie.php?id=<?php echo $row['movie_id']; ?>" class="edit-btn">Chỉnh sửa</a>
                                <a href="delete_movie.php?id=<?php echo $row['movie_id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">Không có phim nào được tìm thấy.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
