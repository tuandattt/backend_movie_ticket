<?php
include '../../includes/config.php';

if (isset($_POST['movie_id']) && isset($_POST['status'])) {
    $movie_id = $_POST['movie_id'];
    $status = $_POST['status'];

    // Cập nhật trạng thái của phim
    $query = "UPDATE movies SET status = ? WHERE movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $movie_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
