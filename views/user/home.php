<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Xử lý tìm kiếm nâng cao
$searchTitle = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
$searchRatingMin = isset($_GET['rating_min']) && $_GET['rating_min'] !== '' ? (float)$_GET['rating_min'] : null;
$searchRatingMax = isset($_GET['rating_max']) && $_GET['rating_max'] !== '' ? (float)$_GET['rating_max'] : null;
$searchStatus = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : '';
$searchGenre = isset($_GET['genre_id']) && $_GET['genre_id'] !== '' ? (int)$_GET['genre_id'] : null;

// Cờ kiểm tra nếu có tìm kiếm
$isSearch = !empty($searchTitle) || $searchRatingMin !== null || $searchRatingMax !== null || !empty($searchStatus) || $searchGenre !== null;

if ($isSearch) {
    // Truy vấn chính cho tìm kiếm
    $searchQuery = "
        SELECT DISTINCT movies.* 
        FROM movies 
        LEFT JOIN movie_genres ON movies.movie_id = movie_genres.movie_id 
        WHERE 1=1";

    // Thêm điều kiện tìm kiếm theo tiêu chí
    if (!empty($searchTitle)) {
        $searchQuery .= " AND movies.title LIKE ?";
    }
    if ($searchRatingMin !== null && $searchRatingMax !== null) {
        $searchQuery .= " AND movies.rating BETWEEN ? AND ?";
    }
    if (!empty($searchStatus)) {
        $searchQuery .= " AND movies.status = ?";
    }
    if ($searchGenre !== null) {
        $searchQuery .= " AND movie_genres.genre_id = ?";
    }

    $stmt = $conn->prepare($searchQuery);

    // Gắn các tham số truy vấn
    $bindTypes = '';
    $bindParams = [];
    if (!empty($searchTitle)) {
        $bindTypes .= 's';
        $bindParams[] = '%' . $searchTitle . '%';
    }
    if ($searchRatingMin !== null && $searchRatingMax !== null) {
        $bindTypes .= 'dd';
        $bindParams[] = $searchRatingMin;
        $bindParams[] = $searchRatingMax;
    }
    if (!empty($searchStatus)) {
        $bindTypes .= 's';
        $bindParams[] = $searchStatus;
    }
    if ($searchGenre !== null) {
        $bindTypes .= 'i';
        $bindParams[] = $searchGenre;
    }

    // Gắn tham số vào truy vấn
    if (!empty($bindParams)) {
        $stmt->bind_param($bindTypes, ...$bindParams);
    }

    $stmt->execute();
    $searchResult = $stmt->get_result();
} else {
    // Lấy các phim mặc định khi không thực hiện tìm kiếm
    $nowShowingQuery = "SELECT * FROM movies WHERE status = 'now_showing' LIMIT 10";
    $nowShowingResult = $conn->query($nowShowingQuery);

    $comingSoonQuery = "SELECT * FROM movies WHERE status = 'coming_soon' LIMIT 10";
    $comingSoonResult = $conn->query($comingSoonQuery);

    $featuredQuery = "SELECT * FROM movies ORDER BY rating DESC LIMIT 10";
    $featuredResult = $conn->query($featuredQuery);
}

// Lấy danh sách thể loại để hiển thị
$genresQuery = "SELECT * FROM genres";
$genresResult = $conn->query($genresQuery);
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
        <!-- Phần tìm kiếm -->
        <h2>Tìm Kiếm Phim</h2>
        <form action="" method="GET" class="movie-search-form">
            <input type="text" name="search_title" placeholder="Nhập tên phim hoặc từ khóa..." value="<?php echo htmlspecialchars($searchTitle); ?>">
            <input type="number" step="0.1" name="rating_min" placeholder="Rating từ..." value="<?php echo htmlspecialchars($searchRatingMin); ?>">
            <input type="number" step="0.1" name="rating_max" placeholder="Rating đến..." value="<?php echo htmlspecialchars($searchRatingMax); ?>">
            <select name="status">
                <option value="">-- Trạng thái --</option>
                <option value="now_showing" <?php echo $searchStatus === 'now_showing' ? 'selected' : ''; ?>>Đang chiếu</option>
                <option value="coming_soon" <?php echo $searchStatus === 'coming_soon' ? 'selected' : ''; ?>>Sắp chiếu</option>
                <option value="stopped" <?php echo $searchStatus === 'stopped' ? 'selected' : ''; ?>>Ngưng chiếu</option>
            </select>
            <select name="genre_id">
                <option value="">-- Thể loại --</option>
                <?php while ($genre = $genresResult->fetch_assoc()): ?>
                    <option value="<?php echo $genre['genre_id']; ?>" <?php echo $searchGenre == $genre['genre_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Tìm kiếm</button>
        </form>

        <!-- Hiển thị kết quả tìm kiếm hoặc danh sách mặc định -->
        <?php if ($isSearch): ?>
            <h2>Kết Quả Tìm Kiếm</h2>
            <div class="movie-list">
                <?php if ($searchResult->num_rows > 0): ?>
                    <?php while ($movie = $searchResult->fetch_assoc()): ?>
                        <div class="movie-item">
                            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
                            <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($movie['status']); ?></p>
                            <p><?php echo htmlspecialchars($movie['description']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không tìm thấy phim phù hợp.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Hiển thị danh sách mặc định -->
            <h2>Phim Đang Chiếu</h2>
            <div class="movie-list">
                <?php while ($movie = $nowShowingResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['description']); ?></p>
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
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
