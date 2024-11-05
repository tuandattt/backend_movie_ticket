<?php
session_start();
include '../../includes/config.php';

$id = $_GET['id'];
$query = "DELETE FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_movies.php");
exit();
?>
