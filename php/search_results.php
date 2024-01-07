<?php
include_once "./base/header.php";
include_once "./database/db.php";

if (isset($_GET['search'])) {
    // Если установлен параметр поиска, выполняем поиск
    $searchTerm = $_GET['search'];
    $searchSql = "SELECT * FROM courses WHERE title LIKE '%$searchTerm%'";
    $searchResult = $conn->query($searchSql);
    $searchCourses = ($searchResult->num_rows > 0) ? $searchResult->fetch_all(MYSQLI_ASSOC) : [];
} else {
    // Если параметр поиска не установлен, перенаправляем на страницу каталога
    header("Location: catalog.php");
    exit();
}

include_once "./base/header.php";
// Пример объявления переменной $currentUserId
$currentUserId = $id; // Замените на ваш вариант

?>

<main class="container mt-5">
    <h2 class="text-center mb-4">Результаты поиска</h2>

    <?php if (!empty($searchCourses)): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($searchCourses as $course): ?>
                <div class="col">
                    <div class="card" style="width: 18rem; margin-top: 20px;">
                        <img src="../image/course/image_course.jpg" class="card-img-top" alt="Course Image">
                        <div class="card-body">
                            <h5 class="card-title">Название: <?php echo $course['title']; ?></h5>
                            <p class="card-text">Описание: <?php echo $course['description']; ?></p>
                            
                            <?php
                            // Получаем информацию о преподавателе
                            $teacherSql = "SELECT name FROM teacher WHERE id = " . $course['teacher_id'];
                            $teacherResult = $conn->query($teacherSql);
                            $teacher = ($teacherResult->num_rows > 0) ? $teacherResult->fetch_assoc() : [];

                            echo '<p class="card-text">Преподаватель: ';
                            if (!empty($teacher)) {
                                echo $teacher['name'];
                            } else {
                                echo 'Нет информации';
                            }
                            echo '</p>';

                            // Проверяем роль пользователя и выводим соответствующую кнопку
                            if ($role == 2) {
                                // Если пользователь - преподаватель, то выводим кнопку "Редактировать" только для его курсов
                                $isUserCourse = ($currentUserId === $course['teacher_id']);
                                if ($isUserCourse) {
                                    echo '<a href="edit_course.php?id=' . $course['id'] . '" class="btn btn-primary">Редактировать</a>';
                                }
                            } elseif ($role == 1) {
                                // Если пользователь - студент, то выводим кнопку "Проходить"
                                echo '<a href="course_details.php?course_id=' . $course['id'] . '" class="btn btn-primary">Проходить</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">Ничего не найдено.</p>
    <?php endif; ?>
</main>

<?php
include_once "./base/footer.php";
?>
