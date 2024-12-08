<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy danh sách phim: Đang chiếu, Sắp chiếu, và Nổi bật
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Movie Booking</title>
</head>
<body>
    <header>
        <h1>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <a href="trending_and_new_movies.php">Phim Mới & Thịnh Hành</a>
            <a href="search.php">Tìm kiếm</a>
            <a href="../../logout.php">Đăng xuất</a>
        </nav>
    </header>

    <main>
        <!-- Phim Đang Chiếu -->
        <section>
            <h2>Phim Đang Chiếu</h2>
            <ul>
                <?php if ($nowShowingResult->num_rows > 0): ?>
                    <?php while ($movie = $nowShowingResult->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($movie['title']); ?></strong><br>
                            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="100"><br>
                            <a href="movie_details.php?movie_id=<?php echo $movie['movie_id']; ?>">Xem Chi Tiết</a>

                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không có phim đang chiếu.</p>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Phim Sắp Chiếu -->
        <section>
            <h2>Phim Sắp Chiếu</h2>
            <ul>
                <?php if ($comingSoonResult->num_rows > 0): ?>
                    <?php while ($movie = $comingSoonResult->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($movie['title']); ?></strong><br>
                            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="100"><br>
                            <a href="movie_details.php?movie_id=<?php echo $movie['movie_id']; ?>">Xem Chi Tiết</a>

                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không có phim sắp chiếu.</p>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Phim Nổi Bật -->
        <section>
            <h2>Phim Nổi Bật</h2>
            <ul>
                <?php if ($featuredResult->num_rows > 0): ?>
                    <?php while ($movie = $featuredResult->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($movie['title']); ?></strong><br>
                            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="100"><br>
                            <a href="movie_details.php?movie_id=<?php echo $movie['movie_id']; ?>">Xem Chi Tiết</a>

                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không có phim nổi bật.</p>
                <?php endif; ?>
            </ul>
        </section>
    </main>
</body>
</html>
