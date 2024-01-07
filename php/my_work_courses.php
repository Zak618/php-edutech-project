<?php
include_once "./base/header.php";
?>

<?php
include_once "../php/database/db.php";
?>

<div class="container mt-5">
    <div class="row row-cols-1 row-cols-md-2 g-4">

        <?php
        $sql = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $description = $row['description'];
                $shortDescription = (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
                echo "
                <div class='col'>
                    <div class='card mb-3'>
                        <div class='card-body'>
                            <h5 class='card-title'>Название курса: {$row['title']}</h5> 
                            <p class='card-text'>$shortDescription</p>
                            <a class='btn btn-primary' href='../../diploma-project/php/edit_course.php?id={$row['id']}'>Редактировать</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
    </div>

    <p style='display: flex; justify-content: center; margin-top: 20px;'>
        <a class='btn btn-primary' href='./create_course.php'>Создать курс</a>
    </p>

<?php
} else {
    echo "
    <p style='text-align: center;'>У вас пока нет созданных курсов.</p>
    <p style='display: flex; justify-content: center;'>
        <a class='btn btn-primary' href='./create_course.php'>Создать курс</a>
    </p>";
}
?>

<?php
include_once "./base/footer.php";
?>
