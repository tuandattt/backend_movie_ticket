<?php
session_start();

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../user/login.php");
    exit();
}
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
            <p>Xin chào, <?php echo $_SESSION['admin']; ?>!</p>
            <a href="../../controllers/logout.php" class="logout-btn">Đăng xuất</a>
        </header>
        
        <nav>
            <ul>
                <li><a href="manage_movies.php">Quản lý Phim</a></li>
                <li><a href="manage_users.php">Quản lý Người dùng</a></li>
                <li><a href="manage_genres.php">Quản lý Thể loại Phim</a></li>
                <li><a href="manage_schedules.php">Quản lý Lịch chiếu</a></li>
                <li><a href="manage_promotions.php">Quản lý Khuyến mãi</a></li>
                <li><a href="reports.php">Thống kê & Báo cáo</a></li>
            </ul>
        </nav>

        <main>
            <section class="overview">
                <h2>Tổng quan</h2>
                <div class="stats">
                    <div class="stat">
                        <h3>Tổng số phim</h3>
                        <p>123</p> <!-- Thay bằng dữ liệu từ cơ sở dữ liệu -->
                    </div>
                    <div class="stat">
                        <h3>Tổng số người dùng</h3>
                        <p>456</p> <!-- Thay bằng dữ liệu từ cơ sở dữ liệu -->
                    </div>
                    <div class="stat">
                        <h3>Số lịch chiếu</h3>
                        <p>78</p> <!-- Thay bằng dữ liệu từ cơ sở dữ liệu -->
                    </div>
                    <div class="stat">
                        <h3>Doanh thu hôm nay</h3>
                        <p>$1000</p> <!-- Thay bằng dữ liệu từ cơ sở dữ liệu -->
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
                    <a href="manage_promotions.php" class="manage-btn">Quản lý Khuyến mãi</a>
                    <a href="reports.php" class="manage-btn">Thống kê & Báo cáo</a>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Movie Booking Admin Dashboard</p>
        </footer>
    </div>
</body>
</html>
