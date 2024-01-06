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

<main class="container mt-5">
    <h2 align="center">Мои курсы</h2>

    <?php if ($success == 1): ?>
        <div class="alert alert-success" role="alert">
            Вы успешно записались на курс!
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($enrolledCourses)): ?>
            <?php foreach ($enrolledCourses as $enrolledCourse): ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="../image/course/image_course.jpg" class="card-img-top" alt="Course Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $enrolledCourse['title']; ?></h5>
                            <p class="card-text"><?php echo $enrolledCourse['description']; ?></p>
                            <a href="course_progress.php?course_id=<?php echo $enrolledCourse['id']; ?>" class="btn btn-primary">Продолжить</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center mt-3">У вас нет записей на курсы.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include_once "./base/footer.php";
?>
