<?php
session_start();

// Redirect to login page if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /movie_booking/views/user/login.php");
    exit();
}

include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';

// Fetch all schedules from the database
$query = "
    SELECT s.schedule_id, m.title AS movie_title, s.show_date, s.show_time, s.theater, s.seats, s.available_seats
    FROM schedules s
    JOIN movies m ON s.movie_id = m.movie_id
    ORDER BY s.show_date ASC, s.show_time ASC
";
$result = $conn->query($query);
$schedules = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Lịch Chiếu</title>
    <link rel="stylesheet" href="/movie_booking/assets/css/admin_style.css">
    <script>
        // Function to delete a schedule
        async function deleteSchedule(scheduleId) {
            if (!confirm('Bạn có chắc muốn xóa lịch chiếu này?')) return;

            const response = await fetch('/movie_booking/controllers/schedule_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_schedule&schedule_id=${scheduleId}`
            });

            const result = await response.json();
            alert(result.message);
            location.reload();
        }
    </script>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Quản Lý Lịch Chiếu</h1>
            <p>Xin chào, <?php echo $_SESSION['username']; ?>!</p>
            <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
        </header>

        <main>
            <!-- Display existing schedules -->
            <section>
                <h2>Danh Sách Lịch Chiếu</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phim</th>
                            <th>Ngày Chiếu</th>
                            <th>Giờ Chiếu</th>
                            <th>Rạp</th>
                            <th>Số Ghế</th>
                            <th>Ghế Còn</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo $schedule['schedule_id']; ?></td>
                                <td><?php echo htmlspecialchars($schedule['movie_title']); ?></td>
                                <td><?php echo $schedule['show_date']; ?></td>
                                <td><?php echo $schedule['show_time']; ?></td>
                                <td><?php echo htmlspecialchars($schedule['theater']); ?></td>
                                <td><?php echo $schedule['seats']; ?></td>
                                <td><?php echo $schedule['available_seats']; ?></td>
                                <td>
                                    <a href="/movie_booking/views/admin/edit_schedule.php?schedule_id=<?php echo $schedule['schedule_id']; ?>">Sửa</a> |
                                    <button onclick="deleteSchedule(<?php echo $schedule['schedule_id']; ?>)">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Form to add a new schedule -->
            <section>
                <h2>Thêm Lịch Chiếu Mới</h2>
                <form action="/movie_booking/controllers/schedule_controller.php" method="POST">
                    <input type="hidden" name="action" value="add_schedule">
                    <label for="movie_id">Phim:</label>
                    <select name="movie_id" required>
                        <?php
                        // Fetch movies for the dropdown
                        $movies_query = "SELECT movie_id, title FROM movies";
                        $movies_result = $conn->query($movies_query);
                        while ($movie = $movies_result->fetch_assoc()) {
                            echo "<option value='{$movie['movie_id']}'>" . htmlspecialchars($movie['title']) . "</option>";
                        }
                        ?>
                    </select>
                    <label for="show_date">Ngày Chiếu:</label>
                    <input type="date" name="show_date" required>
                    <label for="show_time">Giờ Chiếu:</label>
                    <input type="time" name="show_time" required>
                    <label for="theater">Rạp:</label>
                    <input type="text" name="theater" required>
                    <label for="seats">Số Ghế:</label>
                    <input type="number" name="seats" min="1" required>
                    <button type="submit">Thêm Lịch Chiếu</button>
                </form>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Movie Booking Admin Dashboard</p>
        </footer>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Lịch Chiếu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f8ff;
            color: #333;
        }

        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            color: #007BFF;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        main section {
            margin-bottom: 40px;
        }

        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f4f8ff;
        }

        tr:hover {
            background-color: #e6f7ff;
        }

        a, button {
            text-decoration: none;
            padding: 5px 10px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        a:hover, button:hover {
            background-color: #0056b3;
        }

        form {
            margin-top: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input, form select, form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            width: auto;
            color: #fff;
            background-color: #007BFF;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        async function deleteSchedule(scheduleId) {
            if (!confirm('Bạn có chắc muốn xóa lịch chiếu này?')) return;

            const response = await fetch('/movie_booking/controllers/schedule_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_schedule&schedule_id=${scheduleId}`
            });

            const result = await response.json();
            alert(result.message);
            location.reload();
        }
    </script>
</head>

</html>
