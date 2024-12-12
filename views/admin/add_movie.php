<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Kiểm tra nếu admin chưa đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /movie_booking/views/user/login.php");
    exit();
}

// Lấy danh sách thể loại từ cơ sở dữ liệu
$genres_query = "SELECT genre_id, genre_name FROM genres";
$genres_result = $conn->query($genres_query);

if (isset($_POST['add_movie'])) {
    $title = trim($_POST['title']);
    $genre_ids = $_POST['genres']; // Lấy thể loại từ form
    $duration = (int)$_POST['duration'];
    $description = trim($_POST['description']);
    $director = trim($_POST['director']);
    $actors = trim($_POST['actors']);
    $trailer_link = trim($_POST['trailer_link']);
    $release_date = $_POST['release_date'];
    $status = $_POST['status'];
    $rating = (float)$_POST['rating'];

    // Xử lý tải lên hình ảnh
    $poster = $_FILES['poster']['name'];
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/assets/images/';
    $target_file = $target_dir . basename($poster);

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Tạo thư mục nếu chưa tồn tại
    }

    if (move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
        // Thêm phim mới vào bảng `movies`
        $query = "INSERT INTO movies (title, duration, description, director, actors, trailer_link, release_date, status, rating, poster) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisssssdss", $title, $duration, $description, $director, $actors, $trailer_link, $release_date, $status, $rating, $poster);

        if ($stmt->execute()) {
            $movie_id = $stmt->insert_id;

            // Thêm thể loại vào bảng `movie_genres`
            foreach ($genre_ids as $genre_id) {
                $genre_query = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
                $genre_stmt = $conn->prepare($genre_query);
                $genre_stmt->bind_param("ii", $movie_id, $genre_id);
                $genre_stmt->execute();
            }

            // Redirect sau khi thành công
            header("Location: /movie_booking/views/admin/manage_movies.php");
            exit();
        } else {
            echo "Lỗi khi thêm phim: " . $stmt->error;
        }
    } else {
        echo "Lỗi khi tải lên hình ảnh.";
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
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="title">Tên phim:</label>
            <input type="text" name="title" required>
            <br>
            <label for="genres">Thể loại:</label>
            <select name="genres[]" multiple required>
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
            <label for="actors">Diễn viên:</label>
            <input type="text" name="actors" required>
            <br>
            <label for="trailer_link">Trailer (URL):</label>
            <input type="url" name="trailer_link" required>
            <br>
            <label for="release_date">Ngày phát hành:</label>
            <input type="date" name="release_date" required>
            <br>
                <label for="status">Trạng thái:</label>
                <select name="status" required>
                    <option value='coming_soon'>Coming Soon</option>
                    <option value="now_showing">Now Showing</option>
                    <option value="stopped">Stopped</option>
                </select>
                <br>
            <label for="rating">Rating:</label>
            <input type="number" step="0.1" min="0" max="10" name="rating" required>
            <br>
            <label for="poster">Poster:</label>
            <input type="file" name="poster" accept="image/*" required>
            <br>
            <button type="submit" name="add_movie">Thêm phim</button>
        </form>
    </div>
</body>
</html>
