<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

// Предположим, что у вас есть переменная $currentUserId, которая содержит идентификатор текущего пользователя
$currentUserId = $id; // Замените на ваш вариант

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

<main>
    <h2 align="center" style="margin-top: 50px;"><?php echo $course['title']; ?></h2>
    <p align="center"><?php echo $course['description']; ?></p>

    <!-- Вывод информации о модулях -->
    <?php if (!empty($modules)): ?>
        <h3>Модули:</h3>
        <ul>
            <?php foreach ($modules as $module): ?>
                <li><?php echo $module['title']; ?></li>

                <!-- Вывод информации об уроках для каждого модуля -->
                <?php if (!empty($lessons)): ?>
                    <ul>
                        <?php foreach ($lessons as $lesson): ?>
                            <?php if ($lesson['module_id'] == $module['id']): ?>
                                <li><?php echo $lesson['title']; ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php
include_once "./base/footer.php";
?>
