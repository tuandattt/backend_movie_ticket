<?php
session_start();

session_unset();
session_destroy();

header("Location:/movie_booking/views/user/login.php");
exit();
