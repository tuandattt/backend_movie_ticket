<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../user/login.php");
    exit();
}
?>
<!-- Nội dung của trang quản trị sau khi kiểm tra session -->
<h1>Chào mừng Admin đến với bảng điều khiển</h1>
