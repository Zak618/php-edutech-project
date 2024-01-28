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

    // Получение информации о курсе
    $courseSql = "SELECT * FROM courses WHERE id = '$courseId'";
    $courseResult = $conn->query($courseSql);

    if ($courseResult->num_rows > 0) {
        $course = $courseResult->fetch_assoc();

        // Пример SQL-запроса для получения модулей
        $modulesSql = "SELECT * FROM modules WHERE course_id = '$courseId'";
        $modulesResult = $conn->query($modulesSql);
        $modules = [];
        while ($moduleRow = $modulesResult->fetch_assoc()) {
            // Здесь вы можете дополнительно обработать данные о модуле
            $modules[] = $moduleRow;
        }

        // Пример SQL-запроса для получения уроков
        $lessonsSql = "SELECT * FROM lessons WHERE id = '$courseId'";
        $lessonsResult = $conn->query($lessonsSql);
        $lessons = [];
        while ($lessonRow = $lessonsResult->fetch_assoc()) {
            // Здесь вы можете дополнительно обработать данные о уроке
            $lessons[] = $lessonRow;
        }

        // Проверка, записан ли студент на этот курс
        $enrolledSql = "SELECT * FROM student_courses WHERE user_id = '$currentUserId' AND course_id = '$courseId'";
        $enrolledResult = $conn->query($enrolledSql);
        $isEnrolled = ($enrolledResult->num_rows > 0);

        if (isset($_POST['enroll_course'])) {
            // Проверяем, что студент не записан на курс
            if (!$isEnrolled) {
                // Добавляем запись в таблицу student_courses
                $joinDate = date('Y-m-d'); // Получаем текущую дату
                $enrollSql = "INSERT INTO student_courses (user_id, course_id, join_date) VALUES ('$currentUserId', '$courseId', '$joinDate')";
                if ($conn->query($enrollSql) === TRUE) {
                    // Успешно записано, редиректим на страницу "Мои курсы" студента
                    header("Location: ./my_favourate_course.php?user_id=$currentUserId&success=1");
                    exit();
                } else {
                    // Обработка ошибки добавления записи
                    echo "Error: " . $enrollSql . "<br>" . $conn->error;
                }
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
    <h2 class="text-center" style="margin-top: 50px;"><?php echo $course['title']; ?></h2>
    <p class="text-center"><?php echo $course['description']; ?></p>

    <!-- Проверка, записан ли студент на этот курс -->
    <?php if ($role == 1) : ?>
        <?php if (!$isEnrolled) : ?>
            <form method="post" class="text-center mt-3">
                <input type="submit" name="enroll_course" value="Записаться на курс" class="btn btn-primary">
            </form>
        <?php else : ?>
            <form method="post" class="text-center mt-3">
                <input type="submit" name="continue_course" value="Продолжить" class="btn btn-success">
            </form>
            <?php
            if (isset($_POST['continue_course'])) {
                // Редиректим на страницу с модулями и уроками
                echo '<script type="text/javascript">';
                echo 'window.location.href = "course_progress.php?course_id=' . $courseId . '";';
                echo '</script>';

                exit();
            }
            ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Вывод информации о модулях -->
    <?php if (!empty($modules)) : ?>
        <h3 class="text-center mt-4">Модули:</h3>
        <ul class="list-group list-group-flush text-center">
            <?php foreach ($modules as $module) : ?>
                <li class="list-group-item"><?php echo $module['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Вывод информации о уроках -->
    <?php if (!empty($lessons)) : ?>
        <h3 class="text-center mt-4">Уроки:</h3>
        <ul class="list-group list-group-flush text-center">
            <?php foreach ($lessons as $lesson) : ?>
                <li class="list-group-item"><?php echo $lesson['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Отзывы -->
    <div class="mt-5">
        <h3 class="text-center">Отзывы</h3>

        <!-- Средняя оценка за курс -->
        <?php
        // Средняя оценка за курс
        $averageRatingSql = "SELECT AVG(rating) AS average_rating FROM reviews WHERE course_id = '$courseId'";
        $averageRatingResult = $conn->query($averageRatingSql);
        $averageRatingRow = $averageRatingResult->fetch_assoc();
        $averageRating = $averageRatingRow['average_rating'];

        echo '<div class="text-center mt-4">';
        echo '<h4>Средняя оценка за курс: ';

        // Проверяем, что $averageRating не null перед вызовом round()
        if ($averageRating !== null) {
            $averageRating = round($averageRating, 2);
            echo $averageRating;
        } else {
            echo 'Нет оценок'; // Или любое другое значение по умолчанию
        }

        echo '</h4>';
        echo '</div>';
        ?>


        <?php
        // Запрос для получения отзывов
        $reviewsSql = "SELECT * FROM reviews WHERE course_id = '$courseId' ORDER BY created_at DESC";
        $reviewsResult = $conn->query($reviewsSql);

        // Количество отзывов, которые будут показаны на странице
        $reviewsPerPage = 3;

        if ($reviewsResult->num_rows > 0) {
            $totalReviews = $reviewsResult->num_rows;

            // Если отзывов слишком много, покажем последние три
            $reviewsToShow = min($reviewsPerPage, $totalReviews);

            // Выводим отзывы
            for ($i = 0; $i < $reviewsToShow; $i++) {
                $review = $reviewsResult->fetch_assoc();
                echo '<div class="card mt-3">';
                echo '<div class="card-header">' . $review['created_at'] . '</div>';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Отзыв от студента #' . $review['student_id'] . '</h5>';
                echo '<p class="card-text">Оценка: ' . $review['rating'] . '</p>';
                echo '<p class="card-text">Отзыв: ' . $review['review'] . '</p>';
                echo '</div>';
                echo '</div>';
            }

            // Если отзывов больше, чем показано, добавим кнопку "Смотреть еще"
            if ($totalReviews > $reviewsPerPage) {
                echo '<div class="text-center mt-3">';
                echo '<button class="btn btn-primary" id="loadMoreReviews">Смотреть еще</button>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center mt-3">На данный момент отзывов нет.</p>';
        }
        ?>
    </div>


</main>

<script>
    document.getElementById("loadMoreReviews").addEventListener("click", function() {
        // Загружаем следующую порцию отзывов при нажатии на кнопку
        const courseId = <?php echo $courseId; ?>;
        const offset = document.querySelectorAll('.card').length; // Текущее количество отзывов
        const limit = 3; // Количество отзывов для загрузки

        // Отправляем AJAX-запрос к серверу
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'load_more_reviews.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем отзывы на странице
                const newReviews = JSON.parse(xhr.responseText);
                if (newReviews.length > 0) {
                    const reviewsContainer = document.querySelector('.mt-5');
                    newReviews.forEach(function(review) {
                        const card = document.createElement('div');
                        card.classList.add('card', 'mt-3');
                        card.innerHTML = `
                        <div class="card-header">${review.created_at}</div>
                        <div class="card-body">
                            <h5 class="card-title">Отзыв от студента #${review.student_id}</h5>
                            <p class="card-text">Оценка: ${review.rating}</p>
                            <p class="card-text">Отзыв: ${review.review}</p>
                        </div>`;
                        reviewsContainer.appendChild(card);
                    });
                } else {
                    // Если больше нет отзывов, скрываем кнопку
                    document.getElementById("loadMoreReviews").style.display = "none";
                }
            }
        };
        xhr.send(`course_id=${courseId}&offset=${offset}&limit=${limit}`);
    });
</script>

<?php
include_once "./base/footer.php";
?>