<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

// Предположим, что у вас есть переменная $currentUserId, которая содержит идентификатор текущего пользователя
$currentUserId = $id; // Замените на ваш вариант

// Проверяем наличие параметра user_id в URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $currentUserId;

// Проверяем наличие параметра success в URL
$success = isset($_GET['success']) ? $_GET['success'] : 0;

// Получаем информацию о курсах, на которые записан студент
$enrolledSql = "SELECT courses.* FROM student_courses 
                INNER JOIN courses ON student_courses.course_id = courses.id
                WHERE student_courses.user_id = '$user_id'";
$enrolledResult = $conn->query($enrolledSql);
$enrolledCourses = ($enrolledResult->num_rows > 0) ? $enrolledResult->fetch_all(MYSQLI_ASSOC) : [];

?>

<main>
    <h2 align="center" style="margin-top: 50px;">Мои курсы</h2>

    <?php if ($success == 1): ?>
        <div class="alert alert-success" role="alert">
            Вы успешно записались на курс!
        </div>
    <?php endif; ?>

    <?php if (!empty($enrolledCourses)): ?>
        <ul>
            <?php foreach ($enrolledCourses as $enrolledCourse): ?>
                <li><a href="course_progress.php?course_id=<?php echo $enrolledCourse['id']; ?>" class="btn btn-primary btn-sm"><?php echo $enrolledCourse['title']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p align="center">У вас нет записей на курсы.</p>
    <?php endif; ?>
</main>

<?php
include_once "./base/footer.php";
?>
