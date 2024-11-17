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

// Xử lý tìm kiếm thể loại
$searchGenresResult = null;
if (isset($_GET['search_genre']) && !empty($_GET['search_genre'])) {
    $searchTerm = $_GET['search_genre'];
    $searchGenresQuery = "SELECT * FROM genres WHERE genre_name LIKE ?";
    $stmt = $conn->prepare($searchGenresQuery);
    $likeTerm = "%" . $searchTerm . "%";
    $stmt->bind_param("s", $likeTerm);
    $stmt->execute();
    $searchGenresResult = $stmt->get_result();
}
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
            </nav>
        </div>
    </header>

    <div class="movies-section">
        <!-- Phần tìm kiếm thể loại -->
        <h2>Tìm Kiếm Thể Loại</h2>
        <form action="" method="GET" class="genre-search-form">
            <input type="text" name="search_genre" placeholder="Nhập tên thể loại..." value="<?php echo isset($_GET['search_genre']) ? htmlspecialchars($_GET['search_genre']) : ''; ?>">
            <button type="submit">Tìm kiếm</button>
        </form>
        <div class="genre-list">
            <?php if (isset($searchGenresResult) && $searchGenresResult->num_rows > 0): ?>
                <?php while ($genre = $searchGenresResult->fetch_assoc()): ?>
                    <a href="movies_by_genre.php?genre_id=<?php echo $genre['genre_id']; ?>" class="genre-item">
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </a>
                <?php endwhile; ?>
            <?php elseif (isset($searchGenresResult)): ?>
                <p>Không tìm thấy thể loại nào phù hợp.</p>
            <?php endif; ?>
        </div>

        <!-- Phần hiển thị phim đang chiếu -->
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

        <!-- Phần hiển thị phim sắp chiếu -->
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

        <!-- Phần hiển thị phim nổi bật -->
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
