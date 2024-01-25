<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (!isset($_SESSION['role'])) {
    // Сохраняем URL, на который пытается зайти неаутентифицированный пользователь
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    // Перенаправляем на страницу входа
    header("Location: ../../../diploma-project/php/url_auth.php");
    exit();
}

$currentUserId = $id;

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Получите информацию о курсе
    $courseSql = "SELECT * FROM courses WHERE id = '$courseId'";
    $courseResult = $conn->query($courseSql);

    if ($courseResult->num_rows > 0) {
        $course = $courseResult->fetch_assoc();

        // Получите информацию о модулях
        $modulesSql = "SELECT * FROM modules WHERE course_id = '$courseId'";
        $modulesResult = $conn->query($modulesSql);
        $modules = [];
        while ($moduleRow = $modulesResult->fetch_assoc()) {
            $modules[] = $moduleRow;
        }

        // Получите информацию о уроках для каждого модуля
        $lessons = [];
        foreach ($modules as $module) {
            $moduleId = $module['id'];
            $lessonsSql = "SELECT * FROM lessons WHERE module_id = '$moduleId'";
            $lessonsResult = $conn->query($lessonsSql);
            while ($lessonRow = $lessonsResult->fetch_assoc()) {
                $lessons[] = $lessonRow;
            }
        }
    } else {
        // Если курс не найден, редиректим на страницу каталога или другую нужную страницу
        header("Location: catalog.php");
        exit();
    }
} else {
    // Если course_id не передан, редиректим на страницу каталога или другую нужную страницу
    header("Location: catalog.php");
    exit();
}
?>

<main class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4"><?php echo $course['title']; ?></h1>
        <p class="lead"><?php echo $course['description']; ?></p>
    </div>

    <!-- Вывод информации о модулях -->
    <?php if (!empty($modules)) : ?>
        <h3 class="mt-4">Модули:</h3>
        <div class="list-group">
            <?php $moduleCounter = 1; ?>
            <?php foreach ($modules as $module) : ?>
                <a href="#" class="list-group-item list-group-item-action list-group-item-success disabled">
                    <?php echo $moduleCounter . '. ' . $module['title']; ?>
                </a>
                <?php if (!empty($lessons)) : ?>
                    <div class="list-group">
                        <?php $lessonCounter = 1; ?>
                        <?php foreach ($lessons as $lesson) : ?>
                            <?php if ($lesson['module_id'] == $module['id']) : ?>
                                <a href="lesson_details.php?lesson_id=<?php echo $lesson['id']; ?>" class="list-group-item list-group-item-action ml-3 list-group-item-light">
                                    <?php echo $moduleCounter . '.' . $lessonCounter . '. ' . $lesson['title']; ?>
                                </a>
                                <?php $lessonCounter++; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php $moduleCounter++; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <?php
            $totalPointsSql = "SELECT SUM(points) AS total_points FROM progress WHERE student_id = '$currentUserId' AND material_id IN (SELECT id FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId')))";
            $totalPointsResult = $conn->query($totalPointsSql);
            $totalPointsRow = $totalPointsResult->fetch_assoc();
            $totalPoints = $totalPointsRow['total_points'];

            $totalPossiblePointsSql = "SELECT SUM(points) AS total_possible_points FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId'))";
            $totalPossiblePointsResult = $conn->query($totalPossiblePointsSql);
            $totalPossiblePointsRow = $totalPossiblePointsResult->fetch_assoc();
            $totalPossiblePoints = $totalPossiblePointsRow['total_possible_points'];

            if ($totalPossiblePoints > 0) {
                $progressPercentage = ($totalPoints / $totalPossiblePoints) * 100;
            } else {
                $progressPercentage = 0;
            }
            
            $certificateButton = "";
            $feedback = "";

            // Проверяем, есть ли отзыв для данного пользователя по данному курсу
            $existingReviewSql = "SELECT * FROM reviews WHERE student_id = '$currentUserId' AND course_id = '$courseId'";
            $existingReviewResult = $conn->query($existingReviewSql);

                if ($progressPercentage >= 80) {
                    $certificateButton = '<a href="generate_certificate.php?course_id=' . $courseId . '" class="btn btn-primary">Получить сертификат</a>';
                    
                    if ($existingReviewResult->num_rows > 0) {
                        // Отзыв уже существует, выводим соответствующее сообщение и кнопку "Редактировать отзыв"
                        $existingReview = $existingReviewResult->fetch_assoc();
                        echo '<p class="alert alert-warning"><strong>Оценка:</strong> ' . $existingReview['rating'] . '</p>';
                        echo '<p class="alert alert-primary"><strong>Отзыв:</strong> ' . $existingReview['review'] . '</p>';
                        echo '<a href="./database/edit_review.php?review_id=' . $existingReview['id'] . '" class="btn btn-outline-primary" style="margin-bottom:20px;">Редактировать отзыв</a>';
                    } else {
                    $feedback = '
            <form action="./database/submit_review.php" method="post" style="margin-top: 20px;">
                <div class="mb-3">
                    <label for="rating" class="form-label">Оценка</label>
                    <div class="rating">
                        <input type="hidden" name="rating" id="rating" value="0" required>
                        <span class="star" data-value="1">&#9734;</span>
                        <span class="star" data-value="2">&#9734;</span>
                        <span class="star" data-value="3">&#9734;</span>
                        <span class="star" data-value="4">&#9734;</span>
                        <span class="star" data-value="5">&#9734;</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="review" class="form-label">Отзыв</label>
                    <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                </div>
                <input type="hidden" name="course_id" value="' . $courseId . '">
                <button type="submit" class="btn btn-success">Отправить отзыв</button>
            </form>

            <style>
                .rating {
                    display: flex;
                    justify-content: center;
                    cursor: pointer;
                }

                .star {
                    font-size: 35px;
                    margin: 0 5px;
                    color: #ccc;
                    transition: color 0.3s;
                }

                .star.selected {
                    color: gold;
                }
            </style>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const ratingContainer = document.querySelector(".rating");
                    const stars = ratingContainer.querySelectorAll(".star");
                    const ratingInput = document.getElementById("rating");

                    ratingContainer.addEventListener("click", function (event) {
                        const target = event.target;
                        if (target.classList.contains("star")) {
                            const value = target.getAttribute("data-value");
                            ratingInput.value = value;

                            stars.forEach(function (star, index) {
                                if (index < value) {
                                    star.classList.add("selected");
                                } else {
                                    star.classList.remove("selected");
                                }
                            });
                        }
                    });
                });
            </script>
        ';
            }
                }
                echo '<div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: ' . $progressPercentage . '%;" aria-valuenow="' . $progressPercentage . '" aria-valuemin="0" aria-valuemax="100">' . round($progressPercentage, 2) . '%</div>
                </div>';

                echo '<p class="mt-3">Набрано ' . $totalPoints . '/' . $totalPossiblePoints . ' баллов за курс.</p>';

                echo '<div class="mt-3">';
                echo $certificateButton;
                if ($progressPercentage >= 80) {
                    echo $feedback;
                }
                echo '</div>';
            
            ?>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
