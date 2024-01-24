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
                <!-- Используем классы "list-group-item-success" для мятного цвета и "disabled" для невозможности клика -->
                <a href="#" class="list-group-item list-group-item-action list-group-item-success disabled">
                    <?php echo $moduleCounter . '. ' . $module['title']; ?>
                </a>

                <!-- Вывод информации об уроках для каждого модуля -->
                <?php if (!empty($lessons)) : ?>
                    <div class="list-group">
                        <?php $lessonCounter = 1; ?>
                        <?php foreach ($lessons as $lesson) : ?>
                            <?php if ($lesson['module_id'] == $module['id']) : ?>
                                <!-- Используем класс "list-group-item-light" для белого цвета -->
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
            // Получаем сумму баллов студента за курс
            $totalPointsSql = "SELECT SUM(points) AS total_points FROM progress WHERE student_id = '$currentUserId' AND material_id IN (SELECT id FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId')))";
            $totalPointsResult = $conn->query($totalPointsSql);
            $totalPointsRow = $totalPointsResult->fetch_assoc();
            $totalPoints = $totalPointsRow['total_points'];

            // Получаем общее количество баллов доступных за курс
            $totalPossiblePointsSql = "SELECT SUM(points) AS total_possible_points FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId'))";
            $totalPossiblePointsResult = $conn->query($totalPossiblePointsSql);
            $totalPossiblePointsRow = $totalPossiblePointsResult->fetch_assoc();
            $totalPossiblePoints = $totalPossiblePointsRow['total_possible_points'];

            // Проверяем, чтобы не делить на ноль
            if ($totalPossiblePoints > 0) {
                // Вычисляем процент выполнения курса
                $progressPercentage = ($totalPoints / $totalPossiblePoints) * 100;
            } else {
                // Если общее количество баллов равно нулю, устанавливаем процент выполнения в ноль
                $progressPercentage = 0;
            }
            // Проверяем условия для отображения кнопок
            $certificateButton = "";
            if ($progressPercentage >= 80) {
                $certificateButton = '<a href="generate_certificate.php?course_id=' . $courseId . '" class="btn btn-primary">Получить сертификат</a>';
            }



            ?>

            <!-- Добавляем прогресс-бар для визуализации прогресса -->
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $progressPercentage; ?>%;" aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($progressPercentage, 2); ?>%</div>
            </div>

            <p class="mt-3">Набрано <?php echo $totalPoints; ?>/<?php echo $totalPossiblePoints; ?> баллов за курс.</p>

            <div class="mt-3">
                <?php echo $certificateButton; ?>
            </div>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>