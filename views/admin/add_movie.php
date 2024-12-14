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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8ff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #007BFF;
        }

        input, textarea, select {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm Phim Mới</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="title">Tên phim:</label>
            <input type="text" name="title" required>
            
            <label for="genres">Thể loại:</label>
            <select name="genres[]" multiple required>
                <?php while ($row = $genres_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['genre_id']; ?>"><?php echo htmlspecialchars($row['genre_name']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="duration">Thời lượng (phút):</label>
            <input type="number" name="duration" required>
            
            <label for="description">Mô tả:</label>
            <textarea name="description" required></textarea>
            
            <label for="director">Đạo diễn:</label>
            <input type="text" name="director" required>
            
            <label for="actors">Diễn viên:</label>
            <input type="text" name="actors" required>
            
            <label for="trailer_link">Trailer (URL):</label>
            <input type="url" name="trailer_link" required>
            
            <label for="release_date">Ngày phát hành:</label>
            <input type="date" name="release_date" required>
            
            <label for="status">Trạng thái:</label>
            <select name="status" required>
                <option value='coming_soon'>Coming Soon</option>
                <option value="now_showing">Now Showing</option>
                <option value="stopped">Stopped</option>
            </select>
            
            <label for="rating">Rating:</label>
            <input type="number" step="0.1" min="0" max="10" name="rating" required>
            
            <label for="poster">Poster:</label>
            <input type="file" name="poster" accept="image/*" required>
            
            <button type="submit" name="add_movie">Thêm phim</button>
        </form>
        <a href="/movie_booking/views/admin/manage_movies.php" class="back-btn">Quay lại Quản lý Phim</a>
    </div>
</body>
</html>
