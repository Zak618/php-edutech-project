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
        // Получаем материалы урока
        $materialsSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id'";
        $materialsResult = $conn->query($materialsSql);
?>
        <div class="container mt-5">
            <h1>Курс: <?php echo $lessonRow['course_title']; ?></h1>
            <h2>Модуль: <?php echo $lessonRow['module_title']; ?></h2>
            <h3>Урок: <?php echo $lessonRow['lesson_title']; ?></h3>

 
            <?php if ($materialsResult->num_rows > 0) { ?>
                <div class="mt-3">
                    <h4>Материалы урока:</h4>
                    <ul>
                        <?php while ($materialRow = $materialsResult->fetch_assoc()) { ?>
                            <li>
                                <?php
                                // Отобразите информацию о материале в соответствии с его типом
                                if ($materialRow['type'] == 'text') {
                                    echo "Текстовая информация: " . $materialRow['content'];
                                } elseif ($materialRow['type'] == 'test') {
                                    echo "Тестовая задача: " . $materialRow['question'];
                                    // Дополнительная логика для отображения вариантов ответов и правильных ответов
                                }
                                ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } else { ?>
                <p>Урок не содержит материалов.</p>
            <?php } ?>


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
                                    <select class='form-select' name='materialType' id='materialType' required>
                                        <option value='text'>Текстовая информация</option>
                                        <option value='test'>Тестовая задача</option>
                                    </select>
                                </div>

                                <!-- Поля для текстовой информации -->
                                <div class='mb-3' id='textMaterialFields' style='display: none;'>
                                    <label for='textMaterialContent' class='form-label'>Текстовое содержание:</label>
                                    <textarea class='form-control' name='textMaterialContent'></textarea>
                                </div>

                                <div class="mb-3" id="testMaterialFields" style="display: none;">
                                    <!-- Поля для тестовой задачи -->
                                    <label for="testMaterialQuestion" class="form-label">Вопрос:</label>
                                    <input type="text" class="form-control" name="testMaterialQuestion">

                                    <label for="testMaterialOptions" class="form-label">Варианты ответов:</label>
                                    <div id="checkboxContainer">
                                        <!-- Здесь будут отображаться чекбоксы -->
                                    </div>
                                    <button type="button" class="btn btn-success" onclick="addCheckbox()">Добавить вариант ответа</button>
                                    <button type="button" class="btn btn-danger" onclick="removeCheckbox()">Удалить последний вариант ответа</button>

                                    <label for="testMaterialCorrectAnswers" class="form-label">Верные ответы:</label>
                                    <div id="correctCheckboxContainer">
                                        <!-- Здесь будут отображаться чекбоксы для выбора верных ответов -->
                                    </div>

                                </div>

                                <button type='submit' class='btn btn-primary'>Добавить материал</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function toggleMaterialFields() {
                    var materialType = document.getElementById('materialType').value;
                    var textFields = document.getElementById('textMaterialFields');
                    var testFields = document.getElementById('testMaterialFields');

                    textFields.style.display = 'none';
                    testFields.style.display = 'none';

                    if (materialType === 'text') {
                        textFields.style.display = 'block';
                    } else if (materialType === 'test') {
                        testFields.style.display = 'block';
                    }
                }

                function addCheckbox() {
                    var checkboxContainer = document.getElementById('checkboxContainer');
                    var newCheckbox = document.createElement('div');
                    var index = checkboxContainer.children.length + 1;
                    newCheckbox.innerHTML = '<input type="text" class="form-control" name="testMaterialOptions[]" placeholder="Вариант ответа ' + index + '">';
                    checkboxContainer.appendChild(newCheckbox);

                    // Добавляем чекбокс для выбора верного ответа
                    addCorrectCheckbox(index);
                }

                // Функция для удаления последнего чекбокса
                function removeCheckbox() {
                    var checkboxContainer = document.getElementById('checkboxContainer');
                    var correctCheckboxContainer = document.getElementById('correctCheckboxContainer');
                    if (checkboxContainer.children.length > 1) {
                        checkboxContainer.removeChild(checkboxContainer.lastChild);

                        // Удаляем соответствующий чекбокс для выбора верного ответа
                        correctCheckboxContainer.removeChild(correctCheckboxContainer.lastChild);
                    }
                }

                // Функция для добавления чекбокса для выбора верного ответа
                function addCorrectCheckbox(index) {
                    var correctCheckboxContainer = document.getElementById('correctCheckboxContainer');
                    var newCheckbox = document.createElement('div');
                    newCheckbox.innerHTML = '<input type="checkbox" name="correctAnswers[]" value="' + index + '"> Вариант ' + index;
                    correctCheckboxContainer.appendChild(newCheckbox);
                }


                document.getElementById('materialType').addEventListener('change', toggleMaterialFields);


                toggleMaterialFields();
            </script>


    <?php
    } else {
        echo "Урок не найден.";
    }
} else {
    echo "Не указан ID урока.";
}

include_once "./base/footer.php";
    ?>