<?php
include 'includes/header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'home':
        include 'views/home.php';
        break;
    case 'movie_details':
        include 'views/movie_details.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    case 'register':
        include 'views/register.php';
        break;
    default:
        include 'views/404.php';
        break;
}

include 'includes/footer.php';
?>
