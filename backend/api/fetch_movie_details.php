<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web-project/backend/includes/config.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

if (!$movie_id) {
    echo json_encode(["status" => "error", "message" => "Thiếu movie_id"]);
    exit();
}

$query = "
    SELECT m.poster, m.title, m.description, m.director, m.actors, m.duration, m.release_date, m.trailer_link, 
           GROUP_CONCAT(g.genre_name SEPARATOR ', ') AS genres
    FROM movies m
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE m.movie_id = ?
    GROUP BY m.movie_id
";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $movie = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => [
        "poster" => $movie['poster'],
        "title" => $movie['title'],
        "description" => $movie['description'],
        "director" => $movie['director'],
        "actors" => $movie['actors'],
        "duration" => $movie['duration'],
        "release_date" => $movie['release_date'],
        "genres" => explode(", ", $movie['genres']),
        "trailer_link" => $movie['trailer_link'], // Thêm trailer_link
    ]]);
    
} else {
    echo json_encode(["status" => "error", "message" => "Không tìm thấy phim"]);
}
?>
