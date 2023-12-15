<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

// Предположим, что у вас есть переменная $role, которая содержит роль текущего пользователя (студент, преподаватель и т. д.)
// Предположим, что у вас есть переменная $currentUserId, которая содержит идентификатор текущего пользователя (замените на свою переменную)

// Пример объявления переменной $currentUserId
$currentUserId = $id; // Замените на ваш вариант

// Получаем информацию о курсах
$sql = "SELECT courses.id, courses.title, courses.description, teacher.name, courses.teacher_id
        FROM courses
        INNER JOIN teacher ON courses.teacher_id = teacher.id
        WHERE courses.teacher_id IS NOT NULL";
$result = $conn->query($sql);

// Если есть курсы, выводим информацию о каждом курсе
?>
<main>
    <h2 align="center" style="margin-top: 50px;">Топ курсов</h2>
    <h6 align="center" style="margin-top: 20px;">Прошли более 100.000 учеников</h6>

    <div style="margin-top: 50px; display:flex; justify-content: center; flex-wrap: wrap;">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Определяем, принадлежит ли курс текущему пользователю
                $isUserCourse = ($role == 2 && $currentUserId === $row['teacher_id']);

                // Выводим информацию о курсе
                echo '<div class="card" style="width: 18rem; margin-right: 20px; margin-top: 20px;">';
                echo '<img src="../image/course/image_course.jpg" class="card-img-top">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Название: ' . $row['title'] . '</h5>';
                echo '<p class="card-text">Описание: ' . $row['description'] . '</p>';
                echo '<p class="card-text">Преподаватель: ' . $row['name'] . '</p>'; // Выводим имя преподавателя

                // Проверяем роль пользователя и выводим соответствующую кнопку
                if ($role == 2) {
                    // Если пользователь - преподаватель, то выводим кнопку "Редактировать" только для его курсов
                    if ($isUserCourse) {
                        echo '<a href="edit_course.php?id=' . $row['id'] . '" class="btn btn-primary">Редактировать</a>';
                    }
                } elseif ($role == 1) {
                    // Если пользователь - студент, то выводим кнопку "Проходить"
                    echo '<a href="course_details.php?course_id=' . $row['id'] . '" class="btn btn-primary">Проходить</a>';
                }

                echo '</div></div>';
            }
        } else {
            echo '<p align="center">Пока что пусто!<br>Но скоро здесь появятся новые курсы!</p>';
        }
        ?>
    </div>
</main>

<?php
include_once "./base/footer.php";
?>
