<?php
session_start();
include '../../includes/config.php';

// Lấy phim mới (dựa vào ngày phát hành gần đây nhất)
$newMoviesQuery = "SELECT * FROM movies ORDER BY release_date DESC LIMIT 10";
$newMoviesResult = $conn->query($newMoviesQuery);

// Lấy phim thịnh hành (dựa vào đánh giá cao nhất)
$trendingMoviesQuery = "SELECT * FROM movies ORDER BY rating DESC LIMIT 10";
$trendingMoviesResult = $conn->query($trendingMoviesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phim Mới và Thịnh Hành - Movie Booking</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Phim Mới và Thịnh Hành</h1>
        <a href="home.php">Quay lại Trang Chủ</a>
    </header>

    <div class="movies-section">
        <h2>Phim Mới</h2>
        <div class="movie-list">
            <?php if ($newMoviesResult->num_rows > 0): ?>
                <?php while ($movie = $newMoviesResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có phim mới.</p>
            <?php endif; ?>
        </div>

        <h2>Phim Thịnh Hành</h2>
        <div class="movie-list">
            <?php if ($trendingMoviesResult->num_rows > 0): ?>
                <?php while ($movie = $trendingMoviesResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có phim thịnh hành.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
