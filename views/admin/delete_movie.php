<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}
// Lấy ID từ URL
$id = $_GET['id'];

// Xóa bản ghi phim trong bảng `movies`
$query = "DELETE FROM movies WHERE movie_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Xóa thể loại liên quan trong bảng `movie_genres`
    $genre_query = "DELETE FROM movie_genres WHERE movie_id = ?";
    $genre_stmt = $conn->prepare($genre_query);
    $genre_stmt->bind_param("i", $id);
    $genre_stmt->execute();
    
    header("Location: manage_movies.php");
    exit();
} else {
    echo "Đã xảy ra lỗi khi xóa phim: " . $stmt->error;
}
?>
