<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu admin chưa đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

// Thống kê dữ liệu
// Tổng số người dùng
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];

// Tổng số phim
$totalMoviesQuery = "SELECT COUNT(*) AS total_movies FROM movies";
$totalMoviesResult = $conn->query($totalMoviesQuery);
$totalMovies = $totalMoviesResult->fetch_assoc()['total_movies'];

// Tổng số vé đã bán
$totalTicketsQuery = "SELECT COUNT(*) AS total_tickets FROM bookings WHERE status = 'booked'";
$totalTicketsResult = $conn->query($totalTicketsQuery);
$totalTickets = $totalTicketsResult->fetch_assoc()['total_tickets'];

// Tổng doanh thu từ vé
$totalRevenueTicketsQuery = "
    SELECT SUM(
        CASE
            WHEN u.is_u23_confirmed = 'yes' THEN 100000 * 0.82
            WHEN u.membership_level = 'silver' THEN 100000 * 0.95
            WHEN u.membership_level = 'gold' THEN 100000 * 0.90
            WHEN u.membership_level = 'platinum' THEN 100000 * 0.85
            ELSE 100000
        END
    ) AS total_revenue
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'booked'
";
$totalRevenueTicketsResult = $conn->query($totalRevenueTicketsQuery);
$totalRevenueTickets = $totalRevenueTicketsResult->fetch_assoc()['total_revenue'];

// Tổng doanh thu từ đồ ăn
$totalRevenueSnacksQuery = "SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status = 'completed'";
$totalRevenueSnacksResult = $conn->query($totalRevenueSnacksQuery);
$totalRevenueSnacks = $totalRevenueSnacksResult->fetch_assoc()['total_revenue'];

// Thống kê theo thành viên
$membershipStatsQuery = "
    SELECT membership_level, COUNT(*) AS total_users
    FROM users
    WHERE role = 'user'
    GROUP BY membership_level
";
$membershipStatsResult = $conn->query($membershipStatsQuery);

// Thống kê doanh thu theo tháng
$monthlyRevenueQuery = "
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_amount) AS revenue
    FROM orders
    WHERE status = 'completed'
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
";
$monthlyRevenueResult = $conn->query($monthlyRevenueQuery);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê & Báo cáo</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        h2 {
            margin-top: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Thống kê & Báo cáo</h1>

    <!-- Thống kê tổng quan -->
    <section>
        <h2>Tổng quan</h2>
        <table>
            <tr>
                <th>Tổng số người dùng</th>
                <td><?php echo $totalUsers; ?></td>
            </tr>
            <tr>
                <th>Tổng số phim</th>
                <td><?php echo $totalMovies; ?></td>
            </tr>
            <tr>
                <th>Tổng số vé đã bán</th>
                <td><?php echo $totalTickets; ?></td>
            </tr>
            <tr>
                <th>Tổng doanh thu từ vé</th>
                <td><?php echo number_format($totalRevenueTickets, 0); ?> VND</td>
            </tr>
            <tr>
                <th>Tổng doanh thu từ đồ ăn</th>
                <td><?php echo number_format($totalRevenueSnacks, 0); ?> VND</td>
            </tr>
        </table>
    </section>

    <!-- Thống kê theo thành viên -->
    <section>
        <h2>Thống kê theo thành viên</h2>
        <table>
            <thead>
                <tr>
                    <th>Cấp bậc thành viên</th>
                    <th>Số lượng người dùng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $membershipStatsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['membership_level']); ?></td>
                        <td><?php echo $row['total_users']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Doanh thu theo tháng -->
    <section>
        <h2>Doanh thu theo tháng</h2>
        <table>
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Doanh thu (VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $monthlyRevenueResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['month']); ?></td>
                        <td><?php echo number_format($row['revenue'], 0); ?> VND</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <a href="dashboard.php" class="back-btn">Quay lại Dashboard</a>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê & Báo cáo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8ff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            color: #007BFF;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            text-align: left;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .stat-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            background-color: #f9fbff;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #007BFF;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin: 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .stat-cards {
                flex-direction: column;
            }
        }
    </style>
</head>

</html>
