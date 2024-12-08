<?php
include $_SERVER['DOCUMENT_ROOT'] . '/movie_booking/includes/config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Không có quyền truy cập."]);
    exit();
}

// Lấy danh sách các trang tĩnh
if ($_GET['action'] === 'list_pages') {
    $query = "SELECT page_id, page_name, updated_at FROM static_pages";
    $result = $conn->query($query);
    $pages = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($pages);
    exit();
}

// Lấy nội dung trang cụ thể
if ($_GET['action'] === 'get_page') {
    $page_id = intval($_GET['page_id']);
    $query = "SELECT page_name, content FROM static_pages WHERE page_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $page_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $page = $result->fetch_assoc();
    echo json_encode($page);
    exit();
}

// Cập nhật nội dung trang
if ($_POST['action'] === 'update_page') {
    $page_id = intval($_POST['page_id']);
    $content = $_POST['content'];

    $query = "UPDATE static_pages SET content = ? WHERE page_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $content, $page_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Cập nhật nội dung thành công."]);
    } else {
        echo json_encode(["message" => "Không có thay đổi hoặc cập nhật thất bại."]);
    }
    exit();
}
?>
