<?php
session_start();
if (isset($_SESSION['admin'])) {
    echo "<p>Xin chào, " . $_SESSION['admin'] . "</p>";
    echo "<a href='../logout.php'>Đăng xuất</a>";
}
?>
