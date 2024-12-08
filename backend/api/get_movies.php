<?php
// Kết nối cơ sở dữ liệu
header('Access-Control-Allow-Origin: *'); // Cho phép mọi miền truy cập
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Cho phép các phương thức
header('Access-Control-Allow-Headers: Content-Type'); // Cho phép các header cụ thể
include '../includes/config.php'; // Thay đường dẫn phù hợp với file config của bạn

header("Content-Type: application/json");

try {
    // Câu truy vấn để lấy dữ liệu phim
    $query = "SELECT m.movie_id, m.title, m.poster, m.duration, m.release_date, m.status, 
              GROUP_CONCAT(g.genre_name SEPARATOR ', ') AS genres
              FROM movies m
              LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
              LEFT JOIN genres g ON mg.genre_id = g.genre_id
              GROUP BY m.movie_id";

    $result = $conn->query($query);

    // Kiểm tra và xử lý kết quả
    if ($result->num_rows > 0) {
        $movies = [];
        $base_url = "http://localhost/web-project/backend/assets/images/";
        while ($row = $result->fetch_assoc()) {
            $movies[] = [
                'movie_id' => $row['movie_id'],
                'title' => $row['title'],
                'poster' => $base_url . $row['poster'], // Thêm URL đầy đủ cho poster
                'duration' => $row['duration'],
                'release_date' => $row['release_date'],
                'status' => $row['status'],
                'genres' => $row['genres']
            ];
        }
        echo json_encode($movies);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
