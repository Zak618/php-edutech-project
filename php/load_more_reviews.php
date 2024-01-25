<?php
include_once "../php/database/db.php";

if (isset($_POST['course_id']) && isset($_POST['offset']) && isset($_POST['limit'])) {
    $courseId = $_POST['course_id'];
    $offset = $_POST['offset'];
    $limit = $_POST['limit'];

    // Запрос для загрузки дополнительных отзывов
    $reviewsSql = "SELECT * FROM reviews WHERE course_id = '$courseId' ORDER BY created_at DESC LIMIT $offset, $limit";
    $reviewsResult = $conn->query($reviewsSql);

    $reviews = [];
    while ($review = $reviewsResult->fetch_assoc()) {
        $reviews[] = $review;
    }

    // Возвращаем данные в формате JSON
    header('Content-Type: application/json');
    echo json_encode($reviews);
    exit();
}
?>
