<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (isset($_GET['lesson_id'])) {
    $lesson_id = $_GET['lesson_id'];

    // Получаем информацию о уроке
    $lessonSql = "SELECT lessons.title AS lesson_title, modules.title AS module_title, courses.title AS course_title
                  FROM lessons
                  JOIN modules ON lessons.module_id = modules.id
                  JOIN courses ON modules.course_id = courses.id
                  WHERE lessons.id = '$lesson_id'";

    $lessonResult = $conn->query($lessonSql);

    if ($lessonResult->num_rows > 0) {
        $lessonRow = $lessonResult->fetch_assoc();
?>
        <div class="container mt-5">
            <h1>Курс: <?php echo $lessonRow['course_title']; ?></h1>
            <h2>Модуль: <?php echo $lessonRow['module_title']; ?></h2>
            <h3>Урок: <?php echo $lessonRow['lesson_title']; ?></h3>

            <!-- Отображаем материалы урока (если они есть) -->

            <div class="mt-3">
                <!-- Кнопка для добавления материала -->
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMaterialModal">Добавить материал</button>
            </div>

            <!-- Модальное окно для добавления материала -->
            <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addMaterialModalLabel">Добавить материал</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Форма для выбора типа материала -->
                            <form action='../php/database/add_material.php' method='post'>
                                <input type='hidden' name='lesson_id' value='<?php echo $lesson_id; ?>'>

                                <div class='mb-3'>
                                    <label for='materialType' class='form-label'>Выберите тип материала:</label>
                                    <select class='form-select' name='materialType' required>
                                        <option value='text'>Текстовая информация</option>
                                        <option value='test'>Тестовая задача</option>
                                    </select>
                                </div>

                                <button type='submit' class='btn btn-primary'>Добавить материал</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    } else {
        echo "Урок не найден.";
    }
} else {
    echo "Не указан ID урока.";
}

include_once "./base/footer.php";
?>
