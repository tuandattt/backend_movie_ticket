<?php
session_start();
include '../../includes/config.php';

if (isset($_POST['add_genre'])) {
    $genre_name = $_POST['genre_name'];

    $query = "INSERT INTO genres (genre_name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $genre_name);

    if ($stmt->execute()) {
        header("Location: manage_genres.php");
        exit();
    } else {
        echo "Đã xảy ra lỗi khi thêm thể loại: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thể loại</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Thêm Thể loại Mới</h2>
        <form action="" method="POST">
            <label for="genre_name">Tên thể loại:</label>
            <input type="text" name="genre_name" required>
            <br>
            <button type="submit" name="add_genre">Thêm thể loại</button>
        </form>
    </div>
</body>
</html>
