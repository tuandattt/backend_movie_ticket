<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin phim từ URL
$movie_id = isset($_GET['movie_id']) ? intval(value: $_GET['movie_id']) : 0;
if ($movie_id <= 0) {
    echo "Phim không hợp lệ hoặc không tồn tại.";
    exit();
}

// Lấy thông tin phim
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

// Lấy danh sách lịch chiếu
$scheduleQuery = "SELECT * FROM schedules WHERE movie_id = ?";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$schedules = $stmt->get_result();

// Lấy đánh giá
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

// Tính đánh giá trung bình
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