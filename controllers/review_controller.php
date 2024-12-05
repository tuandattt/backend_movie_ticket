<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : null;

    if (!$movieId) {
        $_SESSION['error_message'] = "ID phim không hợp lệ.";
        header("Location: ../../views/user/home.php");
        exit();
    }

    if (isset($_POST['rating'])) {
        $rating = (float)$_POST['rating'];
        $reviewText = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

        $query = "
            INSERT INTO reviews (user_id, movie_id, rating, review_text) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                rating = VALUES(rating), 
                review_text = VALUES(review_text), 
                review_date = CURRENT_TIMESTAMP
        ";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("iids", $userId, $movieId, $rating, $reviewText);
            $stmt->execute();
        }
    }

    if (isset($_POST['comment_text'])) {
        $commentText = trim($_POST['comment_text']);

        $query = "INSERT INTO comments (user_id, movie_id, comment_text) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("iis", $userId, $movieId, $commentText);
            $stmt->execute();
        }
    }

    header("Location: /movie_booking/views/user/movie_details.php?id=$movieId");    
    exit();
}

