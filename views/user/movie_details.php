<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Kiểm tra nếu có ID phim
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($movie_id <= 0) {
    echo "Phim không hợp lệ hoặc không tồn tại.";
    exit();
}

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
if ($result->num_rows === 0) {
    echo "Phim không tồn tại.";
    exit();
}

$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Phim - <?php echo htmlspecialchars($movie['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background-color: #007BFF;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        header nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }
        .movie-details, .trailer-section, .booking-section {
            margin: 20px auto;
            width: 80%;
            max-width: 900px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .poster img {
            max-width: 100%;
            border-radius: 8px;
        }
        .details h2, .trailer-section h2, .booking-section h2 {
            color: #007BFF;
        }
        .booking-button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #28a745;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .booking-button:hover {
            background-color: #218838;
        }
    </style>
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
            <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster'] ?: 'default.jpg'); ?>" 
                 alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="details">
            <h2>Thông Tin Phim</h2>
            <p><strong>Thể Loại:</strong> <?php echo htmlspecialchars($movie['genres'] ?: 'Chưa xác định'); ?></p>
            <p><strong>Thời Lượng:</strong> <?php echo htmlspecialchars($movie['duration'] ?: 'Chưa cập nhật'); ?> phút</p>
            <p><strong>Đạo Diễn:</strong> <?php echo htmlspecialchars($movie['director'] ?: 'Chưa cập nhật'); ?></p>
            <p><strong>Diễn Viên:</strong> <?php echo htmlspecialchars($movie['actors'] ?: 'Chưa cập nhật'); ?></p>
            <p><strong>Ngày Phát Hành:</strong> <?php echo htmlspecialchars($movie['release_date'] ?: 'Chưa cập nhật'); ?></p>
            <p><strong>Đánh Giá:</strong> <?php echo htmlspecialchars($movie['rating'] ?: '0'); ?>/10</p>
            <p><?php echo htmlspecialchars($movie['description'] ?: 'Mô tả chưa được cập nhật.'); ?></p>
        </div>
    </div>

    <div class="trailer-section">
        <h2>Trailer</h2>
        <?php if (!empty($movie['trailer_link'])): ?>
            <iframe width="100%" height="315" 
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
