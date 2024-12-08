<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

$genres_query = "SELECT genre_id, genre_name FROM genres";
$genres_result = $conn->query($genres_query);

if (isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $genre_ids = $_POST['genres'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $director = $_POST['director'];
    $actors = $_POST['actors'];
    $trailer_link = $_POST['trailer_link'];
    $release_date = $_POST['release_date'];

    $poster = $_FILES['poster']['name'];
    $target_dir = "../../assets/images/";
    $target_file = $target_dir . basename($poster);

    if (move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
        $query = "INSERT INTO movies (title, duration, description, director, actors, trailer_link, release_date, poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        $stmt->bind_param("sissssss", $title, $duration, $description, $director, $actors, $trailer_link, $release_date, $poster);

        if ($stmt->execute()) {
            $movie_id = $stmt->insert_id;
            foreach ($genre_ids as $genre_id) {
                $genre_query = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
                $genre_stmt = $conn->prepare($genre_query);
                $genre_stmt->bind_param("ii", $movie_id, $genre_id);
                $genre_stmt->execute();
            }

            header("Location: manage_movies.php");
            exit();
        } else {
            echo "Đã xảy ra lỗi khi thêm phim: " . $stmt->error;
        }
    } else {
        echo "Đã xảy ra lỗi khi tải lên hình ảnh.";
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
            <!-- Tên phim -->
            <label for="title">Tên phim:</label>
            <input type="text" id="title" name="title" required>
            <br>

            <!-- Thể loại -->
            <label for="genres">Thể loại:</label>
            <select id="genres" name="genres[]" multiple required>
                <?php while ($row = $genres_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['genre_id']; ?>">
                        <?php echo htmlspecialchars($row['genre_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>

            <!-- Thời lượng -->
            <label for="duration">Thời lượng (phút):</label>
            <input type="number" id="duration" name="duration" required>
            <br>

            <!-- Mô tả -->
            <label for="description">Mô tả:</label>
            <textarea id="description" name="description" required></textarea>
            <br>

            <!-- Đạo diễn -->
            <label for="director">Đạo diễn:</label>
            <input type="text" id="director" name="director" required>
            <br>

            <!-- Diễn viên -->
            <label for="actors">Diễn viên:</label>
            <input type="text" id="actors" name="actors" required>
            <br>

            <!-- Trailer -->
            <label for="trailer_link">Trailer (URL):</label>
            <input type="url" id="trailer_link" name="trailer_link" required>
            <br>

            <!-- Ngày phát hành -->
            <label for="release_date">Ngày phát hành:</label>
            <input type="date" id="release_date" name="release_date" required>
            <br>

            <!-- Poster -->
            <label for="poster">Poster:</label>
            <input type="file" id="poster" name="poster" accept="image/*" required>
            <br>

            <!-- Nút Thêm phim -->
            <button type="submit" name="add_movie">Thêm phim</button>
        </form>
    </div>
</body>
</html>
