<?php
include_once "./base/header.php";
include_once "../php/database/db.php";
?>

<main>
    <h2 align="center" style="margin-top: 50px;">Топ курсов</h2>
    <h6 align="center" style="margin-top: 20px;">Прошли более 100.000 учеников</h6>

    <div style="margin-top: 50px; display:flex; justify-content: center; flex-wrap: wrap;">
        <?php
        // Запрос к таблице с курсами преподавателей
        $sql = "SELECT courses.title, courses.description, teacher.name FROM courses INNER JOIN teacher ON courses.teacher_id = teacher.id WHERE courses.teacher_id IS NOT NULL";
        $result = $conn->query($sql);

        // Если есть курсы, выводим информацию о каждом курсе
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card" style="width: 18rem; margin-right: 20px; margin-top: 20px;">';
                echo '<img src="../image/course/image_course.jpg" class="card-img-top">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Название: ' . $row['title'] . '</h5>';
                echo '<p class="card-text">Описание: ' . $row['description'] . '</p>';
                echo '<p class="card-text">Преподаватель: ' . $row['name'] . '</p>'; // Выводим имя преподавателя
                echo '<a href="#" class="btn btn-primary">Проходить</a>';
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