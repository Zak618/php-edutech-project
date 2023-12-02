<?php
include_once "./base/header.php";
?>

<?php
include_once "../php/database/db.php";



$sql = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
$result = $conn->query($sql);
//./edit_course.php?id={$row['course_id']} - для редактирования курса
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>Название курса: {$row['title']}</h5>
                <p class='card-text'>Описание курса: {$row['description']}</p>
                <a class='btn btn-primary' href='#'>Редактировать</a>
            </div>
        </div>";
    }
    echo "<a class='btn btn-primary' href='./create_course.php'>Создать курс</a>";
} else {
    echo "
    <p>У вас пока нет созданных курсов.</p>
    <a class='btn btn-primary' href='./create_course.php'>Создать курс</a>";
}


?>

<?php
include_once "./base/footer.php";
?>