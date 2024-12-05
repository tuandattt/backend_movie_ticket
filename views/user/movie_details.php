<?php
session_start();

// Kết nối cơ sở dữ liệu
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /movie_booking/views/user/login.php");
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

if ($result->num_rows === 0) {
    echo "Phim không tồn tại.";
    exit();
}

$movie = $result->fetch_assoc();

// Lấy đánh giá từ người dùng
$reviewsQuery = "
    SELECT r.rating, r.review_date, u.username 
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.movie_id = ?
    ORDER BY r.review_date DESC
";
$stmt = $conn->prepare($reviewsQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$reviewsResult = $stmt->get_result();

// Tính tổng số lượt đánh giá và trung bình rating
$averageRatingQuery = "
    SELECT COUNT(rating) AS total_ratings, AVG(rating) AS average_rating
    FROM reviews
    WHERE movie_id = ?
";
$stmt = $conn->prepare($averageRatingQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$ratingResult = $stmt->get_result()->fetch_assoc();

$totalRatings = $ratingResult['total_ratings'];
$averageRating = $ratingResult['average_rating'] ?? 0;

// Lấy bình luận
$commentsQuery = "
    SELECT c.comment_text, c.comment_date, u.username 
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.movie_id = ?
    ORDER BY c.comment_date DESC
";
$stmt = $conn->prepare($commentsQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$commentsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Phim</title>
</head>
<body>
    <!-- Nút Quay Lại -->
    <p><a href="/movie_booking/views/user/home.php" style="text-decoration: none;">&larr; Quay lại Trang chủ</a></p>

    <h1>Thông Tin Phim</h1>
    <p><strong>Tên Phim:</strong> <?php echo htmlspecialchars($movie['title']); ?></p>
    <p><strong>Thể Loại:</strong> <?php echo htmlspecialchars($movie['genres']); ?></p>
    <p><strong>Thời Lượng:</strong> <?php echo htmlspecialchars($movie['duration']); ?> phút</p>
    <p><strong>Ngày Phát Hành:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
    <p><strong>Mô Tả:</strong> <?php echo htmlspecialchars($movie['description']); ?></p>

    <h2>Trailer</h2>
    <?php if (!empty($movie['trailer_link'])): ?>
        <iframe width="560" height="315" src="<?php echo htmlspecialchars($movie['trailer_link']); ?>" frameborder="0" allowfullscreen></iframe>
    <?php else: ?>
        <p>Không có trailer.</p>
    <?php endif; ?>

    <!-- Nút Đặt Vé -->
    <p>
        <a href="/movie_booking/views/user/booking.php?movie_id=<?php echo $movie_id; ?>" 
           style="text-decoration: none; background-color: #28a745; color: white; padding: 10px 20px; border-radius: 5px;">
            Đặt Vé
        </a>
    </p>

    <h2>Đánh Giá</h2>
    <p><strong>Đánh giá trung bình:</strong> <?php echo number_format($averageRating, 1); ?>/5 từ <?php echo $totalRatings; ?> lượt đánh giá.</p>

    <?php if ($reviewsResult->num_rows > 0): ?>
        <?php while ($review = $reviewsResult->fetch_assoc()): ?>
            <p>
                <strong><?php echo htmlspecialchars($review['username']); ?>:</strong>
                <?php echo htmlspecialchars($review['rating']); ?>/5 
                <small>(<?php echo htmlspecialchars($review['review_date']); ?>)</small>
            </p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Chưa có đánh giá.</p>
    <?php endif; ?>

    <form action="/movie_booking/controllers/review_controller.php" method="POST">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <label for="rating">Đánh Giá:</label>
        <select name="rating" id="rating" required>
            <option value="5">5 - Xuất sắc</option>
            <option value="4">4 - Tốt</option>
            <option value="3">3 - Trung bình</option>
            <option value="2">2 - Kém</option>
            <option value="1">1 - Tệ</option>
        </select>
        <button type="submit">Gửi Đánh Giá</button>
    </form>

    <h2>Bình Luận</h2>
    <?php if ($commentsResult->num_rows > 0): ?>
        <?php while ($comment = $commentsResult->fetch_assoc()): ?>
            <p>
                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                <?php echo htmlspecialchars($comment['comment_text']); ?> 
                <small>(<?php echo htmlspecialchars($comment['comment_date']); ?>)</small>
            </p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Chưa có bình luận.</p>
    <?php endif; ?>

    <form action="/movie_booking/controllers/review_controller.php" method="POST">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <label for="comment_text">Bình Luận:</label>
        <textarea name="comment_text" id="comment_text" rows="4" required></textarea>
        <button type="submit">Gửi Bình Luận</button>
    </form>
</body>
</html>

