<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy thông tin phim hiện tại từ cơ sở dữ liệu
$id = $_GET['id'];
$query = "SELECT * FROM movies WHERE movie_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

// Lấy danh sách thể loại hiện tại của phim
$genres_query = "SELECT genre_id FROM movie_genres WHERE movie_id = ?";
$genres_stmt = $conn->prepare($genres_query);
$genres_stmt->bind_param("i", $id);
$genres_stmt->execute();
$genres_result = $genres_stmt->get_result();
$current_genres = [];
while ($row = $genres_result->fetch_assoc()) {
    $current_genres[] = $row['genre_id'];
}

// Lấy danh sách tất cả các thể loại
$all_genres_query = "SELECT genre_id, genre_name FROM genres";
$all_genres_result = $conn->query($all_genres_query);

if (isset($_POST['update_movie'])) {
    $title = $_POST['title'];
    $genre_ids = $_POST['genres'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $director = $_POST['director'];
    $actors = $_POST['actors'];
    $trailer_link = $_POST['trailer_link'];
    $release_date = $_POST['release_date'];
    
    // Kiểm tra xem có cập nhật hình ảnh không
    if (!empty($_FILES['poster']['name'])) {
        $poster = $_FILES['poster']['name'];
        $target_dir = "../../assets/images/";
        $target_file = $target_dir . basename($poster);

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
            // Cập nhật phim với hình ảnh mới
            $update_query = "UPDATE movies SET title = ?, duration = ?, description = ?, director = ?, actors = ?, trailer_link = ?, release_date = ?, poster = ? WHERE movie_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("sissssssi", $title, $duration, $description, $director, $actors, $trailer_link, $release_date, $poster, $id);
        } else {
            echo "Đã xảy ra lỗi khi tải lên hình ảnh.";
            exit();
        }
    } else {
        // Cập nhật phim không thay đổi hình ảnh
        $update_query = "UPDATE movies SET title = ?, duration = ?, description = ?, director = ?, actors = ?, trailer_link = ?, release_date = ?, WHERE movie_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sisssssi", $title, $duration, $description, $director, $actors, $trailer_link, $release_date, $id);
    }

    if ($update_stmt->execute()) {
        // Cập nhật các thể loại liên quan trong bảng `movie_genres`
        $delete_genre_query = "DELETE FROM movie_genres WHERE movie_id = ?";
        $delete_genre_stmt = $conn->prepare($delete_genre_query);
        $delete_genre_stmt->bind_param("i", $id);
        $delete_genre_stmt->execute();

        foreach ($genre_ids as $genre_id) {
            $insert_genre_query = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
            $insert_genre_stmt = $conn->prepare($insert_genre_query);
            $insert_genre_stmt->bind_param("ii", $id, $genre_id);
            $insert_genre_stmt->execute();
        }

        header("Location: manage_movies.php");
        exit();
    } else {
        echo "Đã xảy ra lỗi khi cập nhật phim: " . $update_stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Phim</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <div class="container">
        <h2>Chỉnh sửa Phim</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="title">Tên phim:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            <br>
            <label for="genres">Thể loại:</label>
            <select name="genres[]" multiple required>
                <?php while ($row = $all_genres_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['genre_id']; ?>" <?php echo in_array($row['genre_id'], $current_genres) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['genre_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="duration">Thời lượng (phút):</label>
            <input type="number" name="duration" value="<?php echo htmlspecialchars($movie['duration']); ?>" required>
            <br>
            <label for="description">Mô tả:</label>
            <textarea name="description" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
            <br>
            <label for="director">Đạo diễn:</label>
            <input type="text" name="director" value="<?php echo htmlspecialchars($movie['director']); ?>" required>
            <br>
            <label for="actors">Diễn viên:</label>
            <input type="text" name="actors" value="<?php echo htmlspecialchars($movie['actors']); ?>" required>
            <br>
            <label for="trailer_link">Trailer (URL):</label>
            <input type="url" name="trailer_link" value="<?php echo htmlspecialchars($movie['trailer_link']); ?>" required>
            <br>
            <label for="release_date">Ngày phát hành:</label>
            <input type="date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
            <br>
            <label for="poster">Poster:</label>
            <input type="file" name="poster" accept="image/*">
            <br>
            <button type="submit" name="update_movie">Cập nhật phim</button>
        </form>
    </div>
</body>
</html>
