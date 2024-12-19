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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Thể loại</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8ff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #007BFF;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
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
            text-align: center;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm Thể loại Mới</h2>
        <form action="" method="POST">
            <label for="genre_name">Tên thể loại:</label>
            <input type="text" name="genre_name" id="genre_name" required>
            <button type="submit" name="add_genre">Thêm thể loại</button>
        </form>
        <a href="http://localhost/web-project/backend/views/admin/manage_genres.php" class="back-btn">Quay lại Quản lý Thể loại</a>
    </div>
</body>
</html>
