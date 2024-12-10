<?php
session_start();

// Xóa tất cả các session đã lưu
session_unset();

// Hủy session
session_destroy();


header("Location: http://localhost:3000/login");
exit();
?>