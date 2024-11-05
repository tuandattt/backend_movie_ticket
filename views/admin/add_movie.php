<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy danh sách thể loại từ cơ sở dữ liệu để chọn trong form
$genres_query = "SELECT genre_id, genre_name FROM genres";
$genres_result = $conn->query($genres_query);

if (isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $genre_id = $_POST['genre'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $director = $_POST['director'];
    $release_date = $_POST['release_date'];

    // Truy vấn để thêm phim mới vào cơ sở dữ liệu
    $query = "INSERT INTO movies (title, genre_id, duration, description, director, release_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siisss", $title, $genre_id, $duration, $description, $director, $release_date);

    if ($stmt->execute()) {
        header("Location: manage_movies.php");
        exit();
    } else {
        echo "Đã xảy ra lỗi khi thêm phim: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thêm Phim Mới</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Thêm Phim Mới</h2>
        <form action="" method="POST">
            <label for="title">Tên phim:</label>
            <input type="text" name="title" required>
            <br>
            <label for="genre">Thể loại:</label>
            <select name="genre" required>
                <option value="">Chọn thể loại</option>
                <?php while ($row = $genres_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['genre_id']; ?>"><?php echo htmlspecialchars($row['genre_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="duration">Thời lượng (phút):</label>
            <input type="number" name="duration" required>
            <br>
            <label for="description">Mô tả:</label>
            <textarea name="description" required></textarea>
            <br>
            <label for="director">Đạo diễn:</label>
            <input type="text" name="director" required>
            <br>
            <label for="release_date">Ngày phát hành:</label>
            <input type="date" name="release_date" required>
            <br>
            <button type="submit" name="add_movie">Thêm phim</button>
        </form>
    </div>
</body>
</html>
