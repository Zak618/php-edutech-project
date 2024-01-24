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

        <div class="container mt-5">
            <form action="../php/database/update_course.php" enctype="multipart/form-data" method="post" class="row g-3 w-50" style="padding:50px; margin-top: 80px; margin-left: auto; margin-right: auto;">
                <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">Название курса:</label>
                    <input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание курса:</label>
                    <textarea class="form-control" name="description"><?php echo $row['description']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Фото курса:</label>
                    <input type="file" class="form-control" name="course_image">
                </div>

                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>

            <?php
            $modulesSql = "SELECT * FROM modules WHERE course_id = '$course_id'";
            $modulesResult = $conn->query($modulesSql);

            if ($modulesResult->num_rows > 0) {
                while ($moduleRow = $modulesResult->fetch_assoc()) {
            ?>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo "Модуль: {$moduleRow['title']}"; ?></h5>
                            <p class="card-text"><?php echo $moduleRow['description']; ?></p>

                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModuleModal<?php echo $moduleRow['id']; ?>">Удалить модуль</button>

                            <!-- Модальное окно для подтверждения удаления модуля -->
                            <div class="modal fade" id="deleteModuleModal<?php echo $moduleRow['id']; ?>" tabindex="-1" aria-labelledby="deleteModuleModalLabel<?php echo $moduleRow['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModuleModalLabel<?php echo $moduleRow['id']; ?>">Подтверждение удаления модуля</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php echo "Вы уверены, что хотите удалить модуль '{$moduleRow['title']}'?"; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            <a href='../php/database/delete_module.php?module_id=<?php echo $moduleRow['id']; ?>' class='btn btn-danger'>Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#newLessonModal<?php echo $moduleRow['id']; ?>">Новый урок</button>

                            <!-- Модальное окно для создания нового урока -->
                            <div class="modal fade" id="newLessonModal<?php echo $moduleRow['id']; ?>" tabindex="-1" aria-labelledby="newLessonModalLabel<?php echo $moduleRow['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="newLessonModalLabel<?php echo $moduleRow['id']; ?>">Новый урок</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action='../php/database/create_lesson.php' method='post'>
                                                <input type='hidden' name='module_id' value='<?php echo $moduleRow['id']; ?>'>

                                                <div class='mb-3'>
                                                    <label for='lessonTitle' class='form-label'>Заголовок урока:</label>
                                                    <input type='text' class='form-control' name='lessonTitle' required>
                                                </div>

                                                <button type='submit' class='btn btn-success'>Создать урок</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $lessonsSql = "SELECT * FROM lessons WHERE module_id = '{$moduleRow['id']}'";
                            $lessonsResult = $conn->query($lessonsSql);

                            if ($lessonsResult->num_rows > 0) {
                                echo "<ul class='list-group mt-2'>";
                                while ($lessonRow = $lessonsResult->fetch_assoc()) {
                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
            <a href='../../diploma-project/php/lesson_details.php?lesson_id={$lessonRow['id']}'>{$lessonRow['title']}</a>
            <span>
                <a href='../php/database/delete_lesson.php?lesson_id={$lessonRow['id']}' class='btn btn-danger btn-sm'>Удалить урок</a>
            </span>
        </li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>У этого модуля пока нет уроков.</p>";
                            }
                            ?>
                        </div>
                    </div>

            <?php
                }
            } else {
                echo "<p>У этого курса пока нет модулей.</p>";
            }
            ?>
            <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#newModuleModal">Новый модуль</button>
            <div class="modal fade" id="newModuleModal" tabindex="-1" aria-labelledby="newModuleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newModuleModalLabel">Новый модуль</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../php/database/create_module.php" method="post">
                                <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">

                                <div class="mb-3">
                                    <label for="moduleTitle" class="form-label">Заголовок модуля:</label>
                                    <input type="text" class="form-control" name="moduleTitle" required>
                                </div>

                                <div class="mb-3">
                                    <label for="moduleDescription" class="form-label">Описание модуля:</label>
                                    <textarea class="form-control" name="moduleDescription" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Создать модуль</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    } else {
        echo "Курс не найден.";
    }
} else {
    echo "Не указан ID курса для редактирования.";
}

include_once "./base/footer.php";
?>