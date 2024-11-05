<?php
session_start();
include '../../includes/config.php';

// Kiểm tra nếu Admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../../views/user/login.php");
    exit();
}

// Xóa thể loại từ cơ sở dữ liệu
$id = $_GET['id'];
$query = "DELETE FROM genres WHERE genre_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_genres.php");
exit();
?>