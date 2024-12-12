<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

include '../../includes/config.php';

// Lấy dữ liệu tổng quan từ cơ sở dữ liệu
$total_movies_query = "SELECT COUNT(*) AS total_movies FROM movies";
$total_movies_result = $conn->query($total_movies_query);
$total_movies = $total_movies_result->fetch_assoc()['total_movies'];

$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total_users'];

$total_schedules_query = "SELECT COUNT(*) AS total_schedules FROM schedules";
$total_schedules_result = $conn->query($total_schedules_query);
$total_schedules = $total_schedules_result->fetch_assoc()['total_schedules'];

$today_revenue_query = "SELECT SUM(total_amount) AS today_revenue FROM orders WHERE DATE(order_date) = CURDATE()";
$today_revenue_result = $conn->query($today_revenue_query);
$today_revenue = $today_revenue_result->fetch_assoc()['today_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css"> <!-- Đường dẫn đến file CSS của Admin -->
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome to Admin Dashboard</h1>
            <p>Xin chào, <?php echo $_SESSION['username']; ?>!</p>
            <a href="../../controllers/logout.php" class="logout-btn">Đăng xuất</a>
        </header>
        
       

        <main>
            <section class="overview">
                <h2>Tổng quan</h2>
                <div class="stats">
                    <div class="stat">
                        <h3>Tổng số phim</h3>
                        <p><?php echo $total_movies; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Tổng số người dùng</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Số lịch chiếu</h3>
                        <p><?php echo $total_schedules; ?></p>
                    </div>
                    <div class="stat">
                        <h3>Doanh thu hôm nay</h3>
                        <p>$<?php echo number_format($today_revenue, 2); ?></p>
                    </div>
                </div>
            </section>

            <section class="management">
                <h2>Quản lý</h2>
                <div class="management-options">
                    <a href="manage_movies.php" class="manage-btn">Quản lý Phim</a>
                    <a href="manage_users.php" class="manage-btn">Quản lý Người dùng</a>
                    <a href="manage_genres.php" class="manage-btn">Quản lý Thể loại Phim</a>
                    <a href="manage_schedules.php" class="manage-btn">Quản lý Lịch chiếu</a>
                    <a href="admin_chat.php" class="manage-btn">Phản hồi user </a>
                    <a href="reports.php" class="manage-btn">Thống kê & Báo cáo</a>
                    <a href="manage_snacks.php" class="manage-btn">Quản lý đồ ăn</a>
                    <a href="admin_bookings.php" class="manage-btn">Quản lý Đặt vé</a>

                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Movie Booking Admin Dashboard</p>
        </footer>
    </div>
</body>
</html>
