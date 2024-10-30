<?php
// Kết nối tới cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'cinema_prj');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xóa phim
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Xử lý form thêm phim mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $title = $_POST['title'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    // Lưu ảnh vào thư mục uploads
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Lưu thông tin phim vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO movies (title, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $target_file);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php");
        exit;
    }
}

// Lấy danh sách phim
$movies = $conn->query("SELECT * FROM movies");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phim</title>
    <style>
        .container {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .movie-box {
            border: 2px solid red;
            width: 150px;
            height: 220px;
            position: relative;
        }
        .movie-box img {
            width: 100%;
            height: 80%;
            object-fit: cover;
        }
        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }
        .add-box {
            border: 2px solid red;
            width: 150px;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .movie-title {
            text-align: center;
            color: blue;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Quản lý phim</h2>

<div class="container">
    <!-- Hiển thị các phim hiện có -->
    <?php while($row = $movies->fetch_assoc()): ?>
    <div class="movie-box">
        <a href="?delete=<?= $row['id'] ?>" class="delete-btn">X</a>
        <img src="<?= $row['image'] ?>" alt="<?= $row['title'] ?>">
        <div class="movie-title"><?= $row['title'] ?></div>
    </div>
    <?php endwhile; ?>

    <!-- Box thêm phim mới -->
    <div class="add-box" onclick="document.getElementById('addMovieForm').style.display='block'">
        <div>+</div>
    </div>
</div>

<!-- Form thêm phim mới -->
<div id="addMovieForm" style="display:none;">
    <h3>Thêm phim mới</h3>
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <label for="title">Tên phim:</label>
        <input type="text" name="title" required><br><br>
        <label for="image">Chọn ảnh:</label>
        <input type="file" name="image" required><br><br>
        <input type="submit" value="Thêm phim">
    </form>
</div>

</body>
</html>
