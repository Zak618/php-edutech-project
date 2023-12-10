<?php
include_once "./base/header.php";
?>

<?php
include_once "../php/database/db.php";
?>

<div class="container d-flex justify-content-center flex-wrap">

<?php
$sql = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
$result = $conn->query($sql);
//./edit_course.php?id={$row['course_id']} - для редактирования курса
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shortDescription = substr($row['description'], 0, 100);
        echo "
        <div class='card mb-3' style='width:50%; margin: 10px;'>
            <div class='card-body'>
                <h5 class='card-title'>Название курса: {$row['title']}</h5> 
                    <p class='card-text'>$shortDescription</p>         
                <a class='btn btn-primary' href='../../diploma-project/php/edit_course.php?id={$row['id']}'>Редактировать</a>
            </div>
        </div>";
    }
    ?>
    </div>
    <?php
    echo "<p style='display:flex; justify-content:center'><a class='btn btn-primary' href='./create_course.php'>Создать курс</a></p>";
} else {
    echo "
    <p>У вас пока нет созданных курсов.</p>
    <a class='btn btn-primary' href='./create_course.php'>Создать курс</a>";
}


?>



<?php
include_once "./base/footer.php";
?>