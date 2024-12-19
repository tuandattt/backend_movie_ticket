<?php
session_start();
include '../../includes/config.php';

// Lấy danh sách phim "Đang chiếu"
$query = "SELECT * FROM movies WHERE status = 'now_showing'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Phim</title>
</head>
<body>
    <h1>Danh Sách Phim</h1>
    <?php while ($movie = $result->fetch_assoc()): ?>
        <div>
            <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
            <a href="schedules.php?movie_id=<?php echo $movie['movie_id']; ?>">Xem Lịch Chiếu</a>
        </div>
    <?php endwhile; ?>
</body>
</html>