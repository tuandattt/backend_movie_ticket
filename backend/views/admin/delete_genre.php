<?php
session_start();
include '../../includes/config.php';

// Xóa thể loại từ cơ sở dữ liệu
$id = $_GET['id'];
$query = "DELETE FROM genres WHERE genre_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_genres.php");
exit();
?>
