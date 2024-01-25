<?php
include_once "../base/header.php";
include_once "../database/db.php";

$currentUserId = $id;

// Проверяем наличие параметра review_id в URL
if (isset($_GET['review_id'])) {
    $reviewId = $_GET['review_id'];

    // Получаем информацию о выбранном отзыве
    $reviewSql = "SELECT * FROM reviews WHERE id = '$reviewId'";
    $reviewResult = $conn->query($reviewSql);

    if ($reviewResult->num_rows > 0) {
        $reviewData = $reviewResult->fetch_assoc();

        // Проверяем, является ли текущий пользователь автором отзыва
        if ($reviewData['student_id'] == $currentUserId) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Обработка формы после отправки
                $courseId = $reviewData['course_id'];
                $rating = $_POST['rating'];
                $editedReview = $_POST['review'];


                // Обновляем данные в таблице reviews
                $updateReviewSql = "UPDATE reviews SET rating = '$rating', review = '$editedReview' WHERE id = '$reviewId'";

                if ($conn->query($updateReviewSql) === TRUE) {
                    // Отзыв успешно обновлен
?>
                    <div class="container mt-5">
                        <div class="row justify-content-center">
                            <div class="col-md-6 text-center">
                                <div class="alert alert-success" role="alert">Отзыв успешно обновлен!</div>
                                <a href="../course_progress.php?course_id=<?php echo $courseId; ?>" class="btn btn-primary mt-3">Назад</a>
                            </div>
                        </div>
                    </div>

            <?php
                    exit();
                } else {
                    // Ошибка при обновлении отзыва
                    echo '<div class="alert alert-danger" role="alert">Ошибка при обновлении отзыва: ' . $conn->error . '</div>';
                }
            }
            // Отображение формы для редактирования отзыва
            ?>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Оценка</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" value="<?php echo $reviewData['rating']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="review" class="form-label">Отзыв</label>
                                <textarea class="form-control" id="review" name="review" rows="3" required><?php echo $reviewData['review']; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Сохранить изменения</button>
                        </form>
                    </div>
                </div>
            </div>

<?php
        } else {
            // Если текущий пользователь не автор отзыва, выводим сообщение
            echo '<div class="alert alert-danger" role="alert">У вас нет прав на редактирование этого отзыва.</div>';
        }
    } else {
        // Если отзыв с указанным ID не найден
        echo '<div class="alert alert-danger" role="alert">Отзыв не найден.</div>';
    }
} else {
    // Если параметр review_id не передан
    echo '<div class="alert alert-danger" role="alert">Отзыв не выбран для редактирования.</div>';
}

include_once "../base/footer.php";
?>