<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

// Предполагаем, что у вас есть информация о пользователе и его роли
$userRole = $role; // Замените на реальное получение роли пользователя

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

        // Предполагаем, что creator_role - это роль создателя курса
        $creatorRole = 2;

        // Проверяем, является ли текущий пользователь создателем курса
        if ($userRole == $creatorRole || $userRole == '1') {
            // Получаем материалы урока
            $materialsSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id'";
            $materialsResult = $conn->query($materialsSql);
?>
            <div class="container mt-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h1 class="card-title">Курс: <?php echo $lessonRow['course_title']; ?></h1>
                        <h2 class="card-subtitle mb-2 text-muted">Модуль: <?php echo $lessonRow['module_title']; ?></h2>
                        <h3 class="card-subtitle mb-2 text-muted">Урок: <?php echo $lessonRow['lesson_title']; ?></h3>
                    </div>
                </div>

                <?php if ($materialsResult->num_rows > 0) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Материалы урока:</h4>
                            <ul>
                                <?php while ($materialRow = $materialsResult->fetch_assoc()) { ?>
                                    <li>
                                        <?php
                                        $materialLink = ($materialRow['type'] == 'text') ? 'material.php' : 'material.php';

                                        $materialLink .= '?material_id=' . $materialRow['id'];
                                        ?>
                                        <a href="<?php echo $materialLink; ?>">
                                            <?php
                                            // Отображаем краткую информацию о материале
                                            echo ($materialRow['type'] == 'text') ? "Текстовая информация" : "Тестовая задача";
                                            ?>
                                        </a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="card-text">Урок не содержит материалов.</p>
                        </div>
                    </div>
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
                                        <label for="testMaterialPoints" class="form-label">Количество баллов:</label>
                                        <input type="number" class="form-control" name="testMaterialPoints" value="0" min="0">

                                        <label for="testMaterialAttempts" class="form-label">Количество попыток (необязательно):</label>
                                        <input type="number" class="form-control" name="testMaterialAttempts" min="1" max="10">

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
            echo "У вас нет прав для редактирования материалов урока.";
        }
    } else {
        echo "Урок не найден.";
    }
} else {
    echo "Не указан ID урока.";
}

include_once "./base/footer.php";
    ?>