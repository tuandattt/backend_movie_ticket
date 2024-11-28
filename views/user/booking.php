<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Lấy ID phim từ URL
$movieId = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : null;

if (!$movieId) {
    echo "Vui lòng chọn phim để đặt vé.";
    exit();
}

// Lấy thông tin lịch chiếu của phim
$query = "
    SELECT schedules.*, movies.title AS movie_title
    FROM schedules
    JOIN movies ON schedules.movie_id = movies.movie_id
    WHERE schedules.movie_id = ? AND schedules.available_seats > 0
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC);

// Kiểm tra nếu không có lịch chiếu
if (count($schedules) === 0) {
    echo "Không có lịch chiếu khả dụng cho phim này.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Vé - <?php echo htmlspecialchars($schedules[0]['movie_title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
        }
        header .header-content h1 {
            margin: 0;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        .booking-page {
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            color: #007BFF;
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <nav>
                <a href="../../logout.php">Đăng xuất</a>
                <a href="home.php">Trang Chủ</a>
            </nav>
        </div>
    </header>

    <div class="booking-page">
        <h2>Đặt Vé - <?php echo htmlspecialchars($schedules[0]['movie_title']); ?></h2>
        <form action="process_booking.php" method="POST">
            <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
            <label for="schedule">Chọn Lịch Chiếu:</label>
            <select name="schedule_id" id="schedule" required>
                <?php foreach ($schedules as $schedule): ?>
                    <option value="<?php echo $schedule['schedule_id']; ?>">
                        <?php echo htmlspecialchars($schedule['show_date'] . " - " . $schedule['show_time'] . " - " . $schedule['theater']); ?> 
                        (Còn <?php echo $schedule['available_seats']; ?> ghế)
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="seats">Chọn Số Ghế:</label>
            <input type="number" name="seats" id="seats" min="1" max="<?php echo $schedules[0]['available_seats']; ?>" required>
            <button type="submit">Xác Nhận</button>
        </form>
    </div>
</body>
</html>
