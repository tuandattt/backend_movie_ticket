<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Kiểm tra nếu có ID phim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Không tìm thấy phim.";
    exit();
}

$movie_id = (int)$_GET['id'];

// Lấy thông tin phim từ cơ sở dữ liệu
$movieQuery = "
    SELECT movies.*, GROUP_CONCAT(genres.genre_name SEPARATOR ', ') AS genres 
    FROM movies 
    LEFT JOIN movie_genres ON movies.movie_id = movie_genres.movie_id 
    LEFT JOIN genres ON movie_genres.genre_id = genres.genre_id 
    WHERE movies.movie_id = ?
    GROUP BY movies.movie_id
";
$stmt = $conn->prepare($movieQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu không có phim
if ($result->num_rows == 0) {
    echo "Phim không tồn tại.";
    exit();
}

$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Phim - <?php echo htmlspecialchars($movie['title']); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            <nav>
                <a href="../../logout.php">Đăng xuất</a>
                <a href="home.php">Trang Chủ</a>
                <a href="search.php">Tìm kiếm</a>
            </nav>
        </div>
    </header>

    <div class="movie-details">
        <div class="poster">
            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="details">
            <h2>Thông Tin Phim</h2>
            <p><strong>Thể Loại:</strong> <?php echo htmlspecialchars($movie['genres']); ?></p>
            <p><strong>Thời Lượng:</strong> <?php echo htmlspecialchars($movie['duration']); ?> phút</p>
            <p><strong>Đạo Diễn:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
            <p><strong>Diễn Viên:</strong> <?php echo htmlspecialchars($movie['actors']); ?></p>
            <p><strong>Ngày Phát Hành:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
            <p><strong>Đánh Giá:</strong> <?php echo htmlspecialchars($movie['rating']); ?>/10</p>
            <p><?php echo htmlspecialchars($movie['description']); ?></p>
        </div>
    </div>

    <div class="trailer-section">
        <h2>Trailer</h2>
        <?php if (!empty($movie['trailer_link'])): ?>
            <iframe width="560" height="315" 
                    src="<?php echo htmlspecialchars($movie['trailer_link']); ?>" 
                    title="Trailer của <?php echo htmlspecialchars($movie['title']); ?>" 
                    frameborder="0" allowfullscreen>
            </iframe>
        <?php else: ?>
            <p>Không có trailer cho phim này.</p>
        <?php endif; ?>
    </div>

    <div class="booking-section">
        <h2>Đặt Vé</h2>
        <a href="booking.php?movie_id=<?php echo $movie['movie_id']; ?>" class="booking-button">Đặt vé ngay</a>
    </div>
</body>
</html>
