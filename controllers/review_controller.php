<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : null;

    if (isset($_POST['rating'])) {
        // Xử lý đánh giá
        $rating = (float)$_POST['rating'];
        $reviewText = trim($_POST['review_text']);

        $query = "
            INSERT INTO reviews (user_id, movie_id, rating, review_text) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text), review_date = CURRENT_TIMESTAMP
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iids", $userId, $movieId, $rating, $reviewText);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Đánh giá của bạn đã được gửi.";
        } else {
            $_SESSION['error_message'] = "Không thể gửi đánh giá.";
        }
    } elseif (isset($_POST['comment_text'])) {
        // Xử lý bình luận
        $commentText = trim($_POST['comment_text']);

        $query = "INSERT INTO comments (user_id, movie_id, comment_text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $userId, $movieId, $commentText);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Bình luận của bạn đã được gửi.";
        } else {
            $_SESSION['error_message'] = "Không thể gửi bình luận.";
        }
    }

    header("Location: ../../views/user/movie_details.php?id=$movieId");
    exit();
}
?>
