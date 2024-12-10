<?php
session_start();
include '../../includes/config.php';

// Lấy ID từ URL và kiểm tra tính hợp lệ
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "ID không hợp lệ.";
    exit();
}

// Xóa bản ghi phim trong bảng `movies`
$query = "DELETE FROM movies WHERE movie_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

// Thực thi câu lệnh và kiểm tra kết quả
if ($stmt->execute()) {
    // Xóa thể loại liên quan trong bảng `movie_genres`
    $genre_query = "DELETE FROM movie_genres WHERE movie_id = ?";
    $genre_stmt = $conn->prepare($genre_query);
    $genre_stmt->bind_param("i", $id);

    // Thực thi câu lệnh xóa thể loại và kiểm tra kết quả
    if ($genre_stmt->execute()) {
        // Nếu xóa thành công, chuyển hướng về trang quản lý phim
        header("Location: manage_movies.php");
        exit();
    } else {
        // Nếu xóa thể loại không thành công
        echo "Đã xảy ra lỗi khi xóa thể loại: " . $genre_stmt->error;
    }
} else {
    // Nếu xóa phim không thành công
    echo "Đã xảy ra lỗi khi xóa phim: " . $stmt->error;
}
?>
