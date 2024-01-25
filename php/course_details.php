<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (!isset($_SESSION['user_id'])) {
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
                $enrollSql = "INSERT INTO student_courses (user_id, course_id) VALUES ('$currentUserId', '$courseId')";
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

<main>
    <h2 align="center" style="margin-top: 50px;"><?php echo $course['title']; ?></h2>
    <p align="center"><?php echo $course['description']; ?></p>

    <!-- Проверка, записан ли студент на этот курс -->
    <?php if ($role == 1): ?>
    <?php if (!$isEnrolled): ?>
        <form method="post">
            <input type="submit" name="enroll_course" value="Записаться на курс" class="btn btn-primary">
        </form>
    <?php else: ?>
        <form method="post">
            <input type="submit" name="continue_course" value="Продолжить" class="btn btn-success">
        </form>
<?php
if (isset($_POST['continue_course'])) {
    // Редиректим на страницу с модулями и уроками
    header("Location: course_progress.php?course_id=$courseId");
    exit();
}
?>
    <?php endif; ?>
<?php endif; ?>

    <!-- Вывод информации о модулях -->
    <?php if (!empty($modules)): ?>
        <h3>Модули:</h3>
        <ul>
            <?php foreach ($modules as $module): ?>
                <li><?php echo $module['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Вывод информации о уроках -->
    <?php if (!empty($lessons)): ?>
        <h3>Уроки:</h3>
        <ul>
            <?php foreach ($lessons as $lesson): ?>
                <li><?php echo $lesson['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php
include_once "./base/footer.php";
?>
