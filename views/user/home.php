<<<<<<< HEAD
=======
<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng tới trang đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy các phim đang chiếu
$nowShowingQuery = "SELECT * FROM movies WHERE status = 'now_showing' LIMIT 10";
$nowShowingResult = $conn->query($nowShowingQuery);

// Lấy các phim sắp chiếu
$comingSoonQuery = "SELECT * FROM movies WHERE status = 'coming_soon' LIMIT 10";
$comingSoonResult = $conn->query($comingSoonQuery);

// Lấy các phim nổi bật (có đánh giá cao nhất)
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
            </nav>
        </div>
    </header>

    <div class="movies-section">
        <h2>Phim Đang Chiếu</h2>
        <div class="movie-list">
            <?php if ($nowShowingResult->num_rows > 0): ?>
                <?php while ($movie = $nowShowingResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có phim đang chiếu.</p>
            <?php endif; ?>
        </div>

        <h2>Phim Sắp Chiếu</h2>
        <div class="movie-list">
            <?php if ($comingSoonResult->num_rows > 0): ?>
                <?php while ($movie = $comingSoonResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có phim sắp chiếu.</p>
            <?php endif; ?>
        </div>

        <h2>Phim Nổi Bật</h2>
        <div class="movie-list">
            <?php if ($featuredResult->num_rows > 0): ?>
                <?php while ($movie = $featuredResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có phim nổi bật.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
>>>>>>> 4c2e2c4054197312eefee3cdfaac26f68551b85a
