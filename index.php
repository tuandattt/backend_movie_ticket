<?php
// Bao gồm các file cấu hình và header
include 'includes/config.php';
include 'includes/header.php';

// Lấy giá trị của tham số 'page' từ URL, mặc định là 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Điều hướng tới các trang dựa trên giá trị của 'page'
switch ($page) {
    case 'home':
        include 'views/user/home.php';
        break;
    case 'movie_details':
        include 'views/user/movie_details.php';
        break;
    case 'search':
        include 'views/user/search.php';
        break;
    case 'login':
        include 'views/user/login.php';
        break;
    case 'register':
        include 'views/user/register.php';
        break;
    case 'profile':
        include 'views/user/profile.php';
        break;
    case 'booking':
        include 'views/user/booking.php';
        break;
    case 'purchase_snacks':
        include 'views/user/purchase_snacks.php';
        break;
    case 'membership_u23':
        include 'views/user/membership_u23.php';
        break;
    case 'membership_levels':
        include 'views/user/membership_levels.php';
        break;
    case 'contact':
        include 'views/user/contact.php';
        break;
    case 'terms':
        include 'views/user/terms.php';
        break;
    default:
        include 'views/user/404.php'; // Trang báo lỗi khi không tìm thấy trang
        break;
}

// Bao gồm footer
include 'includes/footer.php';
?>
