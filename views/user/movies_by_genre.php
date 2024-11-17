<?php
session_start();
include '../../includes/config.php';

if (!isset($_GET['genre_id'])) {
    header("Location: home.php");
    exit();
}

$genre_id = $_GET['genre_id'];

// Lấy tên thể loại
$genreQuery = "SELECT genre_name FROM genres WHERE genre_id = ?";
$stmt = $conn->prepare($genreQuery);
$stmt->bind_param("i", $genre_id);
$stmt->execute();
$genreResult = $stmt->get_result();
$genre = $genreResult->fetch_assoc();

// Lấy danh sách phim thuộc thể loại
$moviesQuery = "
    SELECT movies.* 
    FROM movies
    JOIN movie_genres ON movies.movie_id = movie_genres.movie_id
    WHERE movie_genres.genre_id = ?
";
$stmt = $conn->prepare($moviesQuery);
$stmt->bind_param("i", $genre_id);
$stmt->execute();
$moviesResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phim Thể Loại: <?php echo htmlspecialchars($genre['genre_name']); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Phim Thể Loại: <?php echo htmlspecialchars($genre['genre_name']); ?></h1>
        <a href="home.php">Quay lại Trang chủ</a>
    </header>

    <div class="movies-list">
        <?php if ($moviesResult->num_rows > 0): ?>
            <?php while ($movie = $moviesResult->fetch_assoc()): ?>
                <div class="movie-item">
                    <img src="../../assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <p><strong>Thời lượng:</strong> <?php echo htmlspecialchars($movie['duration']); ?> phút</p>
                    <p><strong>Đánh giá:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không có phim nào trong thể loại này.</p>
        <?php endif; ?>
    </div>
</body>
</html>
