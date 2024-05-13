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
$currentUserId = $id; // Замените на ваш вариант

$lambda = 0.9;
$w1 = 0.3;
$w2 = 0.3;
$w3 = 0.2;
$w4 = 0.2;

$sql = "SELECT courses.id, courses.title, courses.description, teacher.name, courses.teacher_id, courses.image, courses.last_updated
        FROM courses
        INNER JOIN teacher ON courses.teacher_id = teacher.id
        GROUP BY courses.id
        ORDER BY courses.id DESC";
$result = $conn->query($sql);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courseId = $row['id'];
    $F = $conn->query("SELECT AVG(rating) FROM reviews WHERE course_id = '$courseId'")->fetch_assoc()['AVG(rating)'];
    $C = $conn->query("SELECT COUNT(*) FROM certificates WHERE course_name = (SELECT title FROM courses WHERE id = '$courseId')")->fetch_assoc()['COUNT(*)'];
    $U = $conn->query("SELECT COUNT(DISTINCT user_id) FROM student_courses WHERE course_id = '$courseId'")->fetch_assoc()['COUNT(DISTINCT user_id)'];
    // Вычисляем время в днях с последнего обновления
    $daysSinceUpdate = (time() - strtotime($row['last_updated'])) / (60 * 60 * 24);
    $A = exp(-$lambda * ($daysSinceUpdate / 365)); // Вычисляем актуальность как e^(-λ * (t/365))
    // Рассчитываем рейтинг R
    $R = $w1 * $F + $w2 * ($C / 100) + $w3 * log($U + 1) + $w4 * $A;

    // Определение пути к изображению курса
    $defaultImagePath = '../image/course/1.jpg'; // Укажите путь к стандартному изображению
    $imagePath = !empty($row['image']) ? './database/' . $row['image'] : $defaultImagePath;

    $courses[] = [
        'id' => $courseId,
        'title' => $row['title'],
        'description' => $row['description'],
        'teacher' => $row['name'],
        'rating' => $R,
        'image' => $imagePath
    ];
}

// Сортировка курсов по рейтингу
usort($courses, function ($a, $b) {
    return $b['rating'] - $a['rating'];
});

// Отображение только топ 5 курсов
$courses = array_slice($courses, 0, 5);

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

        #card-carousel {
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1500px;
            /* Увеличиваем перспективу для более глубокого 3D эффекта */
            height: 500px;
            position: relative;
            overflow: hidden;
            width: 100%;
        }


        #card-carousel>.card {
            width: 30%;
            /* Уменьшаем ширину карточек */
            min-width: 250px;
            /* Уменьшаем минимальную ширину */
            height: 220px;
            /* Уменьшаем высоту карточек */
            position: absolute;
            transition: transform 1s, opacity 0.5s;
            transform-style: preserve-3d;
            border-radius: 15px;
            /* Меньший радиус для более аккуратного вида */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            backface-visibility: hidden;
            margin: 10px;
            /* Уменьшаем отступы */
        }

        #card-carousel>.card:hover {
            transform: scale(1.05);
            /* Увеличение при наведении */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            /* Более глубокая тень при наведении */
        }

        #card-carousel>.card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            /* Светлее для более заметного эффекта */
            transform: scaleY(-1) translateZ(-1px);
            border-radius: 15px;
            opacity: 0.5;
            /* Более заметная прозрачность */
            pointer-events: none;
        }
    </style>
    

    <img src="../../../diploma-project/image/start/mainLogo.jpg" width="100%">


    <h2 align="center">Топ 5 курсов</h2>
    <div id="card-carousel" style="width: 100%; height: 500px; position: relative; overflow: hidden;">
        <?php foreach ($courses as $course) : ?>
            <div class="card mb-3" style="width: 45%; margin: 10px; border: none; border-radius: 20px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?= $course['image'] ?>" class="img-fluid rounded-start" alt="<?= $course['title'] ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= $course['title'] ?></h5>
                            <p class="card-text mb-0"><?= substr($course['description'], 0, 100) . '...' ?></p>
                            <p class="card-text mb-1">Преподаватель: <?= $course['teacher'] ?></p>
                            <div class="card-actions">
                                <a href="course_details.php?course_id=<?= $course['id'] ?>" class="btn btn-primary">Узнать больше</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div style="background-color: #EFEFEF; padding-top:20px">
        <h2 align="center" style="margin-top: 50px; color: #053163">Новые курсы</h2>
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
                    echo '<h5 class="card-title" style="margin-bottom: 10px;">' . $row['title'] . '</h5>';
                    echo '<p class="card-text" style="margin-bottom: 10px;">' . $shortDescription . '</p>';
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
            ?>
        </div>
    </div>

</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const radius = 300;
        const cards = document.querySelectorAll("#card-carousel .card");
        let angle = 360 / cards.length;
        let baseAngle = 0;

        function updateCards() {
            cards.forEach((card, index) => {
                let cardAngle = baseAngle + (index * angle);
                let radians = cardAngle * Math.PI / 180;
                let yPosition = Math.sin(radians) * 10; // Вертикальное перемещение
                card.style.transform = `rotateY(${cardAngle}deg) translateZ(${radius}px) translateY(${yPosition}px) scale(${index === 0 ? 1.1 : 1})`;
                card.style.opacity = `${index === 0 ? 1 : 0.9}`;
            });
        }

        updateCards();

        setInterval(() => {
            baseAngle = (baseAngle + 1) % 360;
            updateCards();
        }, 100);
    });
</script>
<?php
include_once "./base/footer.php";
?>