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

// Doanh thu từ vé hôm nay
$dailyTicketsRevenueQuery = "
    SELECT SUM(
        CASE
            WHEN u.is_u23_confirmed = 'yes' THEN 60000 * 0.82
            WHEN u.membership_level = 'silver' THEN 60000 * 0.95
            WHEN u.membership_level = 'gold' THEN 60000 * 0.90
            WHEN u.membership_level = 'platinum' THEN 60000 * 0.85
            ELSE 60000
        END
    ) AS daily_tickets_revenue
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'booked' AND DATE(b.booking_date) = CURDATE()";
$dailyTicketsRevenueResult = $conn->query($dailyTicketsRevenueQuery);

if (!$dailyTicketsRevenueResult) {
    die("Error in tickets revenue query: " . $conn->error);
}

$dailyTicketsRevenue = $dailyTicketsRevenueResult->fetch_assoc()['daily_tickets_revenue'] ?? 0;

// Doanh thu từ đồ ăn hôm nay
$dailySnacksRevenueQuery = "
    SELECT SUM(oi.quantity * oi.price) AS daily_snacks_revenue
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'completed' AND oi.item_type = 'snack' AND DATE(o.order_date) = CURDATE()";
$dailySnacksRevenueResult = $conn->query($dailySnacksRevenueQuery);

if (!$dailySnacksRevenueResult) {
    die("Error in snacks revenue query: " . $conn->error);
}

$dailySnacksRevenue = $dailySnacksRevenueResult->fetch_assoc()['daily_snacks_revenue'] ?? 0;

// Tổng doanh thu hôm nay
$dailyTotalRevenue = $dailyTicketsRevenue + $dailySnacksRevenue;

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
    </div>
</section>

<section>
    <h2>Doanh thu hôm nay</h2>
    <table>
        <tr>
            <th>Doanh thu từ vé</th>
            <td><?php echo number_format($dailyTicketsRevenue, 0, ',', '.') . ' VND'; ?></td>
        </tr>
        <tr>
            <th>Doanh thu từ đồ ăn</th>
            <td><?php echo number_format($dailySnacksRevenue, 0, ',', '.') . ' VND'; ?></td>
        </tr>
        <tr>
            <th>Tổng doanh thu hôm nay</th>
            <td><?php echo number_format($dailyTotalRevenue, 0, ',', '.') . ' VND'; ?></td>
        </tr>
    </table>
</section>


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
