<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/user/login.php");
    exit();
}

include '../../includes/config.php';

// Xử lý cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update' && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $username = $_POST['username'] ?? null;
        $name = $_POST['name'] ?? null;
        $age = $_POST['age'] ? intval($_POST['age']) : null;
        $email = $_POST['email'] ?? null;
        $role = $_POST['role'] ?? 'user';
        $membership_level = $_POST['membership_level'] ?? 'bronze';
        $is_u23_confirmed = $_POST['is_u23_confirmed'] ?? 'no';

        $update_query = "
            UPDATE users SET username = ?, name = ?, age = ?, email = ?, role = ?, 
                             membership_level = ?, is_u23_confirmed = ? 
            WHERE user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssissssi", $username, $name, $age, $email, $role, $membership_level, $is_u23_confirmed, $user_id);

        if ($stmt->execute()) {
            $success_message = "Thông tin người dùng đã được cập nhật thành công.";
        } else {
            $error_message = "Có lỗi xảy ra khi cập nhật thông tin người dùng.";
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $delete_query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $success_message = "Tài khoản đã được xóa thành công.";
        } else {
            $error_message = "Có lỗi xảy ra khi xóa tài khoản.";
        }
    }
}

// Lấy danh sách người dùng
$users_query = "SELECT user_id, username, name, age, email, role, membership_level, 
                       is_u23_confirmed, created_at, total_spent 
                FROM users WHERE role != 'admin'";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <h1>Quản lý Người dùng</h1>

    <a href="dashboard.php" style="margin-bottom: 20px; display: inline-block;">&larr; Quay lại Dashboard</a>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Tuổi</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Thành viên</th>
                <th>Xác nhận U23</th>
                <th>Ngày tạo</th>
                <th>Tổng tiền đã tiêu (VNĐ)</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
    <?php while ($user = $users_result->fetch_assoc()): ?>
        <tr data-user-id="<?php echo $user['user_id']; ?>">
            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
            <td class="username"><?php echo htmlspecialchars($user['username']); ?></td>
            <td class="name"><?php echo htmlspecialchars($user['name'] ?? 'Chưa cập nhật'); ?></td>
            <td class="age"><?php echo htmlspecialchars($user['age'] ?? 'N/A'); ?></td>
            <td class="email"><?php echo htmlspecialchars($user['email']); ?></td>
            <td class="role"><?php echo htmlspecialchars($user['role']); ?></td>
            <td class="membership-level"><?php echo htmlspecialchars($user['membership_level']); ?></td>
            <td class="is-u23-confirmed">
                <?php echo $user['is_u23_confirmed'] === 'yes' ? 'Đã xác nhận' : 'Chưa xác nhận'; ?>
            </td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td><?php echo number_format($user['total_spent'], 0); ?> VNĐ</td>
            <td>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="button" onclick="showEditForm(<?php echo $user['user_id']; ?>)">Chỉnh sửa</button>
                </form>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">Xóa</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

    <!-- Form chỉnh sửa người dùng -->
    <div id="editForm" style="display: none; border: 1px solid #ccc; padding: 20px; margin-top: 20px;">
        <h3>Chỉnh sửa thông tin người dùng</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_id" id="edit_user_id">
            <label for="edit_username">Tên đăng nhập:</label>
            <input type="text" name="username" id="edit_username" required><br><br>
            <label for="edit_name">Họ tên:</label>
            <input type="text" name="name" id="edit_name"><br><br>
            <label for="edit_age">Tuổi:</label>
            <input type="number" name="age" id="edit_age" min="0"><br><br>
            <label for="edit_email">Email:</label>
            <input type="email" name="email" id="edit_email" required><br><br>
            <label for="edit_role">Vai trò:</label>
            <select name="role" id="edit_role">
                <option value="user">Người dùng</option>
                <option value="admin">Quản trị viên</option>
            </select><br><br>
            <label for="edit_membership_level">Cấp bậc thành viên:</label>
            <select name="membership_level" id="edit_membership_level">
                <option value="bronze">Đồng</option>
                <option value="silver">Bạc</option>
                <option value="gold">Vàng</option>
                <option value="platinum">Bạch kim</option>
            </select><br><br>
            <label for="edit_is_u23_confirmed">Xác nhận U23:</label>
            <select name="is_u23_confirmed" id="edit_is_u23_confirmed">
                <option value="yes">Đã xác nhận</option>
                <option value="no">Chưa xác nhận</option>
            </select><br><br>
            <button type="submit">Cập nhật</button>
            <button type="button" onclick="hideEditForm()">Hủy</button>
        </form>
    </div>

    <script>
        function showEditForm(userId) {
    // Find the row dynamically using data-user-id
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) {
        alert("Không tìm thấy thông tin người dùng!");
        return;
    }

    // Populate the form fields with the user's data
    document.getElementById("edit_user_id").value = userId;
    document.getElementById("edit_username").value = row.querySelector(".username").textContent.trim();
    document.getElementById("edit_name").value = row.querySelector(".name").textContent.trim();
    document.getElementById("edit_age").value = row.querySelector(".age").textContent.trim();
    document.getElementById("edit_email").value = row.querySelector(".email").textContent.trim();
    document.getElementById("edit_role").value = row.querySelector(".role").textContent.trim();
    document.getElementById("edit_membership_level").value = row.querySelector(".membership-level").textContent.trim();
    document.getElementById("edit_is_u23_confirmed").value =
        row.querySelector(".is-u23-confirmed").textContent.trim() === "Đã xác nhận" ? "yes" : "no";

    // Show the edit form
    document.getElementById("editForm").style.display = "block";
}

function hideEditForm() {
    // Hide the edit form
    document.getElementById("editForm").style.display = "none";
}
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f9fc;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #007BFF;
            margin-top: 20px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        a.back-btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        a.back-btn:hover {
            background-color: #5a6268;
        }

        p {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 8px;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #e9f4ff;
        }

        .btn {
            padding: 6px 12px;
            margin: 2px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-btn {
            background-color: #007BFF;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .edit-form {
            display: none;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-btn:hover {
            background-color: #0056b3;
        }

        .cancel-btn {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .cancel-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>

</html>
