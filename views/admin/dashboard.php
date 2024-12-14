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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f9fc;
            color: #333;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 20px 0;
            background-color: #007BFF;
            color: #fff;
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        header p {
            margin: 10px 0;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            color: #fff;
            background-color: #ff4d4f;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #ff1a1a;
        }

        .overview {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .stat {
            padding: 20px;
            border-radius: 8px;
            background-color: #eaf4fe;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #0056b3;
        }

        .stat p {
            margin-top: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .management {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .management h2 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #0056b3;
        }

        .management-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
        }

        .manage-btn {
            display: block;
            padding: 15px 20px;
            text-align: center;
            text-decoration: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            flex: 1 1 calc(33.333% - 10px);
        }

        .manage-btn:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #007BFF;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>

</html>
