<?php
include_once "../base/header.php";
include_once "../database/db.php";

$currentUserId = $id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка формы после отправки
    $courseId = $_POST['course_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Проверяем, существует ли уже отзыв для данного пользователя по данному курсу
    $existingReviewSql = "SELECT * FROM reviews WHERE student_id = '$currentUserId' AND course_id = '$courseId'";
    $existingReviewResult = $conn->query($existingReviewSql);

    if ($existingReviewResult->num_rows > 0) {
        // Отзыв уже существует, выводим сообщение и кнопку "Редактировать отзыв"
        $existingReview = $existingReviewResult->fetch_assoc();
        echo '<div class="alert alert-info" role="alert">Ваш отзыв:</div>';
        echo '<p><strong>Оценка:</strong> ' . $existingReview['rating'] . '</p>';
        echo '<p><strong>Отзыв:</strong> ' . $existingReview['review'] . '</p>';
        echo '<a href="edit_review.php?review_id=' . $existingReview['id'] . '" class="btn btn-warning">Редактировать отзыв</a>';
    } else {
        // Вставка данных в таблицу reviews
        $insertReviewSql = "INSERT INTO reviews (student_id, course_id, rating, review) VALUES ('$currentUserId', '$courseId', '$rating', '$review')";

        if ($conn->query($insertReviewSql) === TRUE) {
            // Отзыв успешно добавлен
            echo '<div class="alert alert-success" role="alert">Отзыв успешно отправлен!</div>';
            // Перенаправление на страницу course_progress.php
            header("Location: ../course_progress.php?course_id=" . $courseId);
            exit();
        } else {
            // Ошибка при добавлении отзыва
            echo '<div class="alert alert-danger" role="alert">Ошибка при отправке отзыва: ' . $conn->error . '</div>';
        }
    }
}

include_once "../base/footer.php";
?>
