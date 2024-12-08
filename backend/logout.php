<?php
session_start();

session_unset();
session_destroy();

header("Location:/backend/views/user/login.php");
exit();