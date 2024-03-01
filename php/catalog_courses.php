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
// Пример объявления переменной $currentUserId
$currentUserId = $id;

// Получаем информацию о курсах
$sql = "SELECT courses.id, courses.title, courses.description, teacher.name, courses.teacher_id
        FROM courses
        INNER JOIN teacher ON courses.teacher_id = teacher.id
        WHERE courses.teacher_id IS NOT NULL";
$result = $conn->query($sql);

$userCoursesSql = "SELECT course_id FROM student_courses WHERE user_id = '$currentUserId'";
$userCoursesResult = $conn->query($userCoursesSql);
$userCourses = [];
while ($row = $userCoursesResult->fetch_assoc()) {
    $userCourses[] = $row['course_id'];
}

// Если есть курсы, выводим информацию о каждом курсе
?>
<main>
    <style>
        /* Стили для кнопки при обычном состоянии */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        /* Стили для кнопки при наведении */
        .btn-primary:hover {
            background-color: #fff;
            border-color: #007bff;
            color: #007bff;
        }
    </style>
    <div style="background-color: #EFEFEF; padding-top:20px">
        <h2 align="center" style="margin-top: 50px; color: #053163">Курсы</h2>
        <p align="center" style="margin-top: 20px; font-size: 20px; font-weight: 200;">Начинай учиться прямо сейчас.</p>

        <div style="margin-top: 50px; display:flex; justify-content: center; flex-wrap: wrap;">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Определяем, принадлежит ли курс текущему пользователю
                    $isUserCourse = ($role == 2 && $currentUserId === $row['teacher_id']);
                    $isUserTakingCourse = in_array($row['id'], $userCourses);
                    // Обрезаем описание до 100 символов и добавляем многоточие, если описание длиннее
                    $shortDescription = strlen($row['description']) > 100 ? substr($row['description'], 0, 100) . '...' : $row['description'];
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

                    // Получаем среднюю оценку для курса
                    $averageRatingSql = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE course_id = '{$row['id']}'";
                    $averageRatingResult = $conn->query($averageRatingSql);
                    $averageRating = ($averageRatingResult->num_rows > 0) ? $averageRatingResult->fetch_assoc()['avg_rating'] : null;

                    // Выводим информацию о курсе с изображением слева
                    echo '<div class="card mb-3" style="width: 45%; margin-right: 20px; border: none; border-radius: 20px;">';
                    echo '<div class="row g-0">';
                    echo '<div class="col-md-4" style="padding-right: 20px;">';
                    echo '<img src="' . $courseImage . '" class="img-fluid rounded-start" alt="' . $row['title'] . '">';
                    echo '</div>';
                    echo '<div class="col-md-8">';
                    echo '<div class="card-body" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; padding: 0px;">';

                    echo '<div>';
                    echo '<h5 class="card-title" style="margin-bottom: 10px;">Название: ' . $row['title'] . '</h5>';
                    echo '<p class="card-text" style="margin-bottom: 10px;">Описание: ' . $shortDescription . '</p>';
                    echo '<p class="card-text" style="margin-bottom: 10px;">Преподаватель: ' . $row['name'] . '</p>'; // Выводим имя преподавателя
                    echo '</div>';

                    // Выводим среднюю оценку, если она существует
                    if ($averageRating !== null) {
                        echo '<div class="d-flex align-items-center" style="margin-top: auto;">';
                        echo '<p class="card-text me-2" style="margin-bottom: 5px;">Средняя оценка: <span class="text-warning"><i class="fas fa-star"></i></span> ' . number_format($averageRating, 1) . '</p>';
                        echo '</div>';
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
                    echo '</div></div></div></div></div>';
                }
            } else {
                echo '<p align="center">Пока что пусто!<br>Но скоро здесь появятся новые курсы!</p>';
            }
            // Получаем уникальные категории
            $categoriesSql = "SELECT DISTINCT categories.id, categories.name FROM categories
INNER JOIN courses ON categories.id = courses.category_id";
            $categoriesResult = $conn->query($categoriesSql);
            $categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);

            ?>
            
            <div style="margin-bottom: 90px;">
            <h2 align="center" style="margin-top: 50px; color: #053163">Категории</h2>
            <!-- Овальная форма для отображения категорий -->
            <div class="d-flex justify-content-center mt-4">
                <?php
                foreach ($categories as $category) {
                    echo '<button type="button" class="btn btn-primary me-2" data-category="' . $category['id'] . '">' . $category['name'] . '</button>';
                }
                ?>
            </div>
            </div>
        </div>
    </div>

</main>
<script>
    // Скрипт для фильтрации курсов по категориям
    document.addEventListener("DOMContentLoaded", function () {
        const categoryButtons = document.querySelectorAll('.btn-primary');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-category');
                window.location.href = 'filtered_courses.php?category=' + categoryId;
            });
        });
    });
</script>

<?php
include_once "./base/footer.php";
?>