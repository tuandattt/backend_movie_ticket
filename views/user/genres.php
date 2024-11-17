<?php
session_start();
include '../../includes/config.php';

// Lấy danh sách thể loại từ cơ sở dữ liệu
$genresQuery = "SELECT * FROM genres";
$genresResult = $conn->query($genresQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh Mục Thể Loại - Movie Booking</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Danh Mục Thể Loại</h1>
        <a href="home.php">Quay lại Trang Chủ</a>
    </header>

    <div class="genres-list">
        <?php if ($genresResult->num_rows > 0): ?>
            <ul>
                <?php while ($genre = $genresResult->fetch_assoc()): ?>
                    <li><a href="movies_by_genre.php?genre_id=<?php echo $genre['genre_id']; ?>"><?php echo htmlspecialchars($genre['genre_name']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Không có thể loại nào.</p>
        <?php endif; ?>
    </div>
</body>
</html>
