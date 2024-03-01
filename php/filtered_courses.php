<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

$currentUserId = $id;
// Получаем информацию о курсах пользователя
$userCourses = [];
if ($currentUserId) {
    $userCoursesSql = "SELECT course_id FROM student_courses WHERE user_id = '$currentUserId'";
    $userCoursesResult = $conn->query($userCoursesSql);

    while ($row = $userCoursesResult->fetch_assoc()) {
        $userCourses[] = $row['course_id'];
    }
}

if (isset($_GET['category'])) {
    $category_id = $_GET['category'];

    $sql = "SELECT courses.id, courses.title, courses.description, teacher.name, courses.teacher_id
            FROM courses
            INNER JOIN teacher ON courses.teacher_id = teacher.id
            WHERE courses.teacher_id IS NOT NULL AND courses.category_id = '$category_id'";
    $result = $conn->query($sql);

    echo '<main>';
    echo '<div class="container mt-5">';

    // Вывод категории, чтобы пользователь видел, что выбрана конкретная категория
    $categoryNameSql = "SELECT name FROM categories WHERE id = '$category_id'";
    $categoryNameResult = $conn->query($categoryNameSql);
    $categoryName = $categoryNameResult->fetch_assoc()['name'];
    echo '<h2 align="center" style="margin-top: 50px; color: #053163">Курсы по категории: ' . $categoryName . '</h2>';

    // Вывод курсов
    echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" style="margin-top: 50px;">';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Определяем, принадлежит ли курс текущему пользователю
            $isUserCourse = ($role == 2 && $currentUserId === $row['teacher_id']);
            $isUserTakingCourse = in_array($row['id'], $userCourses);

            // Обрезаем описание до 100 символов и добавляем многоточие, если описание длиннее
            $shortDescription = strlen($row['description']) > 100 ? substr($row['description'], 0, 100) . '...' : $row['description'];

            // Получаем среднюю оценку для курса
            $averageRatingSql = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE course_id = '{$row['id']}'";
            $averageRatingResult = $conn->query($averageRatingSql);
            $averageRating = ($averageRatingResult->num_rows > 0) ? $averageRatingResult->fetch_assoc()['avg_rating'] : null;

            // Получаем информацию о фото курса из базы данных
            $sqlImage = "SELECT image FROM `courses` WHERE id = '{$row['id']}'";
            $resultImage = $conn->query($sqlImage);

            // Используем изображение из базы данных, если оно существует, иначе используем дефолтное изображение
            if ($resultImage->num_rows > 0) {
                $imageData = $resultImage->fetch_assoc()['image'];
                // Проверяем, что значение не пустое
                if (!empty($imageData)) {
                    $courseImage = './database/' . $imageData;
                } else {
                    $courseImages = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
                    $randomImage = $courseImages[array_rand($courseImages)];
                    $courseImage = '../image/course/' . $randomImage;
                }
            } else {
                $courseImages = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
                $randomImage = $courseImages[array_rand($courseImages)];
                $courseImage = '../image/course/' . $randomImage;
            }

            // Выводим информацию о курсе с изображением слева
            echo '<div class="col">';
            echo '<div class="card h-100">';
            echo '<img src="' . $courseImage . '" class="card-img-top" alt="' . $row['title'] . '">';
            echo '<div class="card-body d-flex flex-column">';
            echo '<h5 class="card-title">' . $row['title'] . '</h5>';
            echo '<p class="card-text">' . $shortDescription . '</p>';
            echo '<p class="card-text"><small class="text-muted">Преподаватель: ' . $row['name'] . '</small></p>';

            // Выводим среднюю оценку, если она существует
            if ($averageRating !== null) {
                echo '<p class="card-text"><small class="text-muted">Средняя оценка: <span class="text-warning"><i class="fas fa-star"></i></span> ' . number_format($averageRating, 1) . '</small></p>';
            }

            // Проверяем роль пользователя и выводим соответствующую кнопку
            echo '<div class="mt-auto">';
            if ($role == 2) {
                // Если пользователь - преподаватель, то выводим кнопку "Редактировать" только для его курсов
                if ($isUserCourse) {
                    echo '<a href="edit_course.php?id=' . $row['id'] . '" class="btn btn-primary">Редактировать</a>';
                }
            } elseif ($role == 1) {
                if ($isUserTakingCourse) {
                    // Если пользователь уже проходит курс, выводим зеленую кнопку "Продолжить"
                    echo '<a href="course_details.php?course_id=' . $row['id'] . '" class="btn btn-success">Продолжить</a>';
                } else {
                    // Если пользователь не проходит курс, выводим обычную кнопку "Проходить"
                    echo '<a href="course_details.php?course_id=' . $row['id'] . '" class="btn btn-primary">Записать на курс</a>';
                }
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p align="center" class="mt-5">Пока что пусто!<br>Но скоро здесь появятся новые курсы!</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</main>';
} else {
    echo "Не указана категория для фильтрации.";
}

include_once "./base/footer.php";
?>
