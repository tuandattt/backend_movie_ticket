<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /movie_booking/views/user/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đánh Giá và Bình Luận</title>
    <script>
        // Lấy dữ liệu đánh giá từ server
        async function fetchReviews() {
            const response = await fetch('/movie_booking/controllers/admin_feedback_controller.php?action=list_reviews');
            const reviews = await response.json();
            const container = document.getElementById('reviews');
            reviews.forEach(r => {
                const div = document.createElement('div');
                div.innerHTML = `
                    <p>Phim: ${r.movie_title} - Người dùng: ${r.username}</p>
                    <p>Đánh giá: ${r.rating}/5 - Nội dung: ${r.comment}</p>
                    <p>Ngày: ${r.review_date}</p>
                    <button onclick="deleteReview(${r.review_id})">Xóa</button>
                    <hr>
                `;
                container.appendChild(div);
            });
        }

        // Xóa đánh giá
        async function deleteReview(review_id) {
            const response = await fetch('/movie_booking/controllers/admin_feedback_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: action=delete_review&review_id=${review_id}
            });
            const result = await response.json();
            alert(result.message);
            location.reload();
        }

        // Lấy dữ liệu bình luận từ server
        async function fetchComments() {
            const response = await fetch('/movie_booking/controllers/admin_feedback_controller.php?action=list_comments');
            const comments = await response.json();
            const container = document.getElementById('comments');
            comments.forEach(c => {
                const div = document.createElement('div');
                div.innerHTML = `
                    <p>Phim: ${c.movie_title} - Người dùng: ${c.username}</p>
                    <p>Bình luận: ${c.comment_text}</p>
                    <p>Ngày: ${c.comment_date}</p>
                    <button onclick="deleteComment(${c.comment_id})">Xóa</button>
                    <hr>
                `;
                container.appendChild(div);
            });
        }

        // Xóa bình luận
        async function deleteComment(comment_id) {
            const response = await fetch('/movie_booking/controllers/admin_feedback_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: action=delete_comment&comment_id=${comment_id}
            });
            const result = await response.json();
            alert(result.message);
            location.reload();
        }

        // Gọi các hàm khi trang tải
        document.addEventListener('DOMContentLoaded', () => {
            fetchReviews();
            fetchComments();
        });
    </script>
</head>
<body>
    <h1>Quản Lý Đánh Giá và Bình Luận</h1>
    <h2>Đánh Giá</h2>
    <div id="reviews"></div>

    <h2>Bình Luận</h2>
    <div id="comments"></div>
</body>
</html>