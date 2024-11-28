<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Nhận dữ liệu tìm kiếm từ phương thức GET
$searchTitle = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
$searchRatingMin = isset($_GET['rating_min']) && $_GET['rating_min'] !== '' ? (float)$_GET['rating_min'] : null;
$searchRatingMax = isset($_GET['rating_max']) && $_GET['rating_max'] !== '' ? (float)$_GET['rating_max'] : null;
$searchStatus = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : '';
$searchGenre = isset($_GET['genre_id']) && $_GET['genre_id'] !== '' ? (int)$_GET['genre_id'] : null;

// Truy vấn cơ sở dữ liệu
$query = "
    SELECT DISTINCT m.*
    FROM movies AS m
    LEFT JOIN movie_genres AS mg ON m.movie_id = mg.movie_id
    WHERE 1=1";

$bindTypes = '';
$bindParams = [];

// Điều kiện tìm kiếm
if (!empty($searchTitle)) {
    $query .= " AND m.title LIKE ?";
    $bindTypes .= 's';
    $bindParams[] = '%' . $searchTitle . '%';
}
if ($searchRatingMin !== null && $searchRatingMax !== null) {
    $query .= " AND m.rating BETWEEN ? AND ?";
    $bindTypes .= 'dd';
    $bindParams[] = $searchRatingMin;
    $bindParams[] = $searchRatingMax;
}
if (!empty($searchStatus)) {
    $query .= " AND m.status = ?";
    $bindTypes .= 's';
    $bindParams[] = $searchStatus;
}
if ($searchGenre !== null) {
    $query .= " AND mg.genre_id = ?";
    $bindTypes .= 'i';
    $bindParams[] = $searchGenre;
}

$stmt = $conn->prepare($query);

if (!empty($bindParams)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}

$stmt->execute();
$searchResult = $stmt->get_result();

// Truy vấn danh sách thể loại
$genresQuery = "SELECT * FROM genres";
$genresResult = $conn->query($genresQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tìm Kiếm Phim</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Tìm Kiếm Phim</h1>
            <nav>
                <a href="../../logout.php">Đăng xuất</a>
                <a href="home.php">Trang chủ</a>
            </nav>
        </div>
    </header>

    <div class="search-section">
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

        <div class="movie-list">
            <?php if ($searchResult->num_rows > 0): ?>
                <?php while ($movie = $searchResult->fetch_assoc()): ?>
                    <div class="movie-item">
                        <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster'] ?: 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
                        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($movie['status']); ?></p>
                        <p><?php echo htmlspecialchars(substr($movie['description'], 0, 100)); ?>...</p>
                        <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>">Xem chi tiết</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không tìm thấy phim phù hợp.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
