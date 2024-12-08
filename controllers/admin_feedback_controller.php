<?php
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Không có quyền truy cập."]);
    exit();
}

// API: Liệt kê đánh giá
if ($_GET['action'] === 'list_reviews') {
    $query = "
        SELECT r.review_id, r.rating, r.comment, r.review_date, u.username, m.title AS movie_title
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        JOIN movies m ON r.movie_id = m.movie_id
        ORDER BY r.review_date DESC
    ";
    $result = $conn->query($query);
    $reviews = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($reviews);
    exit();
}

// API: Liệt kê bình luận
if ($_GET['action'] === 'list_comments') {
    $query = "
        SELECT c.comment_id, c.comment_text, c.comment_date, u.username, m.title AS movie_title
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        JOIN movies m ON c.movie_id = m.movie_id
        ORDER BY c.comment_date DESC
    ";
    $result = $conn->query($query);
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($comments);
    exit();
}

// API: Xóa đánh giá
if ($_POST['action'] === 'delete_review') {
    $review_id = intval($_POST['review_id']);
    $query = "DELETE FROM reviews WHERE review_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Đánh giá đã được xóa."]);
    } else {
        echo json_encode(["message" => "Xóa đánh giá thất bại."]);
    }
    exit();
}

// API: Xóa bình luận
if ($_POST['action'] === 'delete_comment') {
    $comment_id = intval($_POST['comment_id']);
    $query = "DELETE FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Bình luận đã được xóa."]);
    } else {
        echo json_encode(["message" => "Xóa bình luận thất bại."]);
    }
    exit();
}
?>
