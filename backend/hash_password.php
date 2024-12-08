<?php
$password = 'admin123'; // Mật khẩu bạn muốn tạo
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Mật khẩu đã mã hóa: " . $hashed_password;
