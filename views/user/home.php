<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy các phim mặc định
$nowShowingQuery = "SELECT * FROM movies WHERE status = 'now_showing' LIMIT 10";
$nowShowingResult = $conn->query($nowShowingQuery);

$comingSoonQuery = "SELECT * FROM movies WHERE status = 'coming_soon' LIMIT 10";
$comingSoonResult = $conn->query($comingSoonQuery);

$featuredQuery = "SELECT * FROM movies ORDER BY rating DESC LIMIT 10";
$featuredResult = $conn->query($featuredQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Movie Booking</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Xin chào, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
            <nav>
                <a href="../../logout.php">Đăng xuất</a>
                <a href="trending_and_new_movies.php">Phim thịnh hành</a>
                <a href="search.php">Tìm kiếm</a>
            </nav>
        </div>
    </header>

    <div class="movies-section">
        <h2>Phim Đang Chiếu</h2>
        <div class="movie-list">
            <?php while ($movie = $nowShowingResult->fetch_assoc()): ?>
                <div class="movie-item">
                    <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    <!-- Liên kết đến movie_details.php -->
                    <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" class="details-link">Xem Chi Tiết</a>
                </div>
            <?php endwhile; ?>
        </div>

        <h2>Phim Sắp Chiếu</h2>
        <div class="movie-list">
            <?php while ($movie = $comingSoonResult->fetch_assoc()): ?>
                <div class="movie-item">
                    <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    <!-- Liên kết đến movie_details.php -->
                    <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" class="details-link">Xem Chi Tiết</a>
                </div>
            <?php endwhile; ?>
        </div>

        <h2>Phim Nổi Bật</h2>
        <div class="movie-list">
            <?php while ($movie = $featuredResult->fetch_assoc()): ?>
                <div class="movie-item">
                    <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    <!-- Liên kết đến movie_details.php -->
                    <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" class="details-link">Xem Chi Tiết</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
