<?php
include_once "./base/header.php";
include_once "../php/database/db.php";


if (isset($_GET['id'])) {
    $course_id = $_GET['id'];


    $sql = "SELECT * FROM courses WHERE id = '$course_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
        <form action="../php/database/update_course.php" method="post" class="row g-3 w-50" style="padding:50px; margin-top: 80px; margin-left: auto; margin-right: auto;">
            <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Название курса:</label>
                <input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание курса:</label>
                <textarea class="form-control" name="description"><?php echo $row['description']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>

<?php
    } else {
        echo "Курс не найден.";
    }
} else {
    echo "Не указан ID курса для редактирования.";
}

include_once "./base/footer.php";
?>