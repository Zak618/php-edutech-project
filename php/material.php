<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
    /* Стили для навигационной панели */
.nav-pills .nav-item.active {
    background-color: #AFDAFC; /* Цвет фона для активного элемента */
}

.nav-pills .nav-link.active {
    color: #212529; /* Цвет текста для активного элемента */
}

</style>
<?php
include_once "../../diploma-project/php/base/header.php";
include_once "../php/database/db.php";

if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];

    $materialSql = "SELECT * FROM materials WHERE id = '$material_id'";
    $materialResult = $conn->query($materialSql);

    if ($materialResult->num_rows > 0) {
        $materialRow = $materialResult->fetch_assoc();
        $lesson_id = $materialRow['lesson_id'];


        // Получаем все материалы в уроке, упорядоченные по ID
        $allMaterialsSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id' ORDER BY id";
        $allMaterialsResult = $conn->query($allMaterialsSql);
        $allMaterials = [];
        while ($row = $allMaterialsResult->fetch_assoc()) {
            $allMaterials[] = $row;
        }

        // Находим текущий индекс материала в массиве
        $currentIndex = array_search($materialRow, $allMaterials);

        // Получаем предыдущий материал
        $prevMaterial = null;
        for ($i = $currentIndex - 1; $i >= 0; $i--) {
            $prevMaterial = $allMaterials[$i];
            break;
        }

        // Получаем следующий материал
        $nextMaterial = null;
        for ($i = $currentIndex + 1; $i < count($allMaterials); $i++) {
            $nextMaterial = $allMaterials[$i];
            break;
        }
?>
<!-- Навигационная панель -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            Навигация по заданиям
        </div>
        <div class="card-body">
            <ul class="nav nav-pills">
                <?php
                foreach ($allMaterials as $index => $navMaterial) {
                    $navMaterialLink = ($navMaterial['type'] == 'text') ? 'material.php' : 'material.php';
                    $navMaterialLink .= '?material_id=' . $navMaterial['id'];

                    // Получаем информацию о прогрессе студента для данного материала
                    $navProgressSql = "SELECT * FROM progress WHERE student_id = '$id' AND material_id = '{$navMaterial['id']}'";
                    $navProgressResult = $conn->query($navProgressSql);

                    $navItemClass = 'nav-item';
                    $navLinkClass = 'nav-link';

                    if ($navMaterial['id'] == $material_id) {
                        // Текущий элемент
                        $navItemClass .= ' active';
                        
                    } elseif ($navProgressResult->num_rows > 0) {
                        $navProgressRow = $navProgressResult->fetch_assoc();
                        if ($navProgressRow['points'] > 0) {
                            // Если студент сдал материал, добавляем зеленую галочку
                            $navItemClass .= ' bg-success';
                            $navLinkClass .= ' text-white';
                        } else {
                            // Если студент не сдал материал, добавляем красный крестик
                            $navItemClass .= ' bg-danger';
                            $navLinkClass .= ' text-white';
                        }
                    } else {
                        // Если материал не пройден, используем цвет по умолчанию
                        $navItemClass .= ' bg-light';
                        $navLinkClass .= ' text-dark';
                    }
                    ?>
                    <li class="<?php echo $navItemClass; ?>">
                        <a class="<?php echo $navLinkClass; ?>" href="<?php echo $navMaterialLink; ?>">
                            <?php
                            echo ($navMaterial['type'] == 'text') ? "Текстовая информация" : "Тестовая задача";
                            ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>


        <div class="container mt-5">
            <div class="card mb-3">
                <div class="card-body">
                    <?php
                    if ($materialRow['type'] == 'text') {
                        echo "<h1 class='card-title'>Текстовая информация</h1>";
                        echo "<p>{$materialRow['content']}</p>";
                        if ($role == 2 && $materialRow['type'] == 'text') {
                            echo "<div class=\"mb-3\">";
                            echo "<button class=\"btn btn-primary\" onclick=\"editMaterial(" . $materialRow['id'] . ")\">Редактировать</button>";
                            echo "<button class=\"btn btn-danger\" onclick=\"removeMaterial(" . $materialRow['id'] . ")\">Удалить материал</button>";
                            echo "</div>";
                        }
                    } elseif ($materialRow['type'] == 'test') {
                        echo "<h1 class='card-title'>Тестовая задача</h1>";



                        echo "<form id='answerForm'>";
                        echo "<input type='hidden' name='material_id' value='$material_id'>";

                        echo "<div class='mt-3'>";
                        echo "<p id='totalScore'></p>";
                        echo "<p id='attemptsLeft'></p>";
                        echo "</div>";

                        echo "<p>Вопрос: {$materialRow['question']}</p>";

                        $options = explode(",", $materialRow['options']);
                        echo "<p>Варианты ответов:</p>";
                        foreach ($options as $index => $option) {
                            echo "<div class='form-check'>";
                            echo "<input class='form-check-input' type='checkbox' name='selectedAnswers[]' value='$index' id='option$index'>";
                            echo "<label class='form-check-label' for='option$index'>$option</label>";
                            echo "</div>";
                        }

                        echo "<button type='button' id='answerButton' class='btn btn-primary mt-3' onclick='checkAnswers(" . $material_id . ")'>Ответить</button>";
                        if ($role == 2) {
                            echo "<button class='btn btn-danger' onclick='deleteTest(" . $material_id . ")'>Удалить тест</button>";
                        }

                        echo "</form>";
                        echo "<div class='mt-3' id='resultContainer'></div>";
                    }
                    ?>

                </div>
            </div>


            <!-- Модальное окно для редактирования текстового материала -->
            <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMaterialModalLabel">Редактировать текстовый материал</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Форма для редактирования текстового материала -->
                            <form id="editMaterialForm" action="../../diploma-project/php/database/update_material.php" method="post">
                                <input type="hidden" name="material_id" value="<?php echo $materialRow['id']; ?>">
                                <label for="materialContent" class="form-label">Текстовое содержание:</label>
                                <textarea class="form-control" name="materialContent"><?php echo $materialRow['content']; ?></textarea>
                                <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
                            </form>
                        </div>
                    </div>
                </div>

            <div class="mt-3">
                <?php
                if ($prevMaterial) {
                    echo "<a class='btn btn-secondary' href='material.php?material_id=" . $prevMaterial['id'] . "'>Назад</a>";
                }
                if ($nextMaterial) {
                    echo "<a class='btn btn-secondary float-end' href='material.php?material_id=" . $nextMaterial['id'] . "'>Далее</a>";
                }
                ?>
            </div>
        </div>

        <script>
            function checkAnswers(materialId) {
    fetch('../php/database/check_answers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                material_id: materialId,
                selectedAnswers: getSelectedAnswers()
            }),
        })
        .then(response => response.text())
        .then(resultText => {
            console.log('Result Text:', resultText);
            console.log('Содержимое ответа сервера перед парсингом:', resultText);
            try {
                var resultData = JSON.parse(resultText);

                // Вне зависимости от правильности ответа
                fetch('../php/database/update_progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        material_id: materialId,
                        points: <?php echo $materialRow['points']; ?>,
                        is_correct: resultData.result !== undefined ? resultData.result : false,
                        student_id: <?php echo $id; ?>
                    }),
                })
                .then(response => response.text())
                .then(updateResult => {
                    // Обработка результата обновления
                    try {
                        var updateData = JSON.parse(updateResult);
                        if (updateData.status === 'success') {
                            console.log('Баллы успешно начислены!');
                            console.log(updateData.attempts_left);
                            handleAttemptsLeft(updateData.attempts_left, resultData.result);
                        } else {
                            console.error('Ошибка при обновлении баллов:', updateData.message);
                        }
                    } catch (updateError) {
                        console.error('Ошибка при парсинге JSON ответа об обновлении баллов:', updateError);
                        console.log('Содержимое ответа сервера:', updateResult);
                    }
                })
                .catch(error => {
                    console.error('Ошибка при обновлении баллов:', error);
                });

                // Показываем сообщение о результате
                showResultMessage(resultData.result ? 'Верно!' : 'Неверно!', resultData.result ? 'alert-success' : 'alert-danger');
                
            } catch (error) {
                console.error('Ошибка при парсинге JSON:', error);
                console.error('Ошибка при парсинге JSON:', error);
                console.log('Содержимое ответа сервера:', resultText);
            }
        })
        .catch(error => {
            console.error('Ошибка при обработке ответа:', error);
        });
}


function handleAttemptsLeft(attemptsLeft, isCorrect) {
    var totalScoreContainer = document.getElementById('totalScore');
    var attemptsLeftContainer = document.getElementById('attemptsLeft');

    totalScoreContainer.textContent = 'Вы набрали ' + (isCorrect ? <?php echo $materialRow['points']; ?> : 0) + ' баллов';
    attemptsLeftContainer.textContent = 'У вас осталось ' + attemptsLeft + '/' + <?php echo $materialRow['attempts']; ?> + ' попыток';


    if (attemptsLeft == 0) {
    // Если попытки закончились, блокируем кнопку и отображаем сообщение
    document.getElementById('answerButton').classList.add('disabled');
    document.getElementById('answerButton').disabled = true;

    // Показываем сообщение о том, что попытки закончились
    var attemptsMessage = document.createElement('div');
    attemptsMessage.className = 'alert alert-danger';
    attemptsMessage.textContent = 'Все попытки израсходованы. Задание больше не доступно для прохождения.';

    var resultContainer = document.getElementById('resultContainer');
    resultContainer.innerHTML = '';
    resultContainer.appendChild(attemptsMessage);
}

}



            function getSelectedAnswers() {
                var selectedAnswers = [];
                var checkboxes = document.querySelectorAll('input[name="selectedAnswers[]"]:checked');
                checkboxes.forEach(function(checkbox) {
                    selectedAnswers.push(checkbox.value);
                });
                return selectedAnswers;
            };

            function showResultMessage(message, alertClass) {
                var resultMessage = document.createElement('div');
                resultMessage.className = 'alert ' + alertClass;
                resultMessage.textContent = message;

                var resultContainer = document.getElementById('resultContainer');
                resultContainer.innerHTML = '';
                resultContainer.appendChild(resultMessage);
            };

            function deleteTest(materialId) {
                if (confirm('Вы уверены, что хотите удалить этот материал?')) {
                    $.ajax({
                        type: 'POST',
                        url: './database/delete_test.php',
                        data: {
                            material_id: materialId
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                window.location.href = './lesson_details.php?lesson_id=<?php echo $lesson_id; ?>';
                            } else {
                                console.error('Ошибка удаления материала: ', result.message);
                            }
                        },
                        error: function(error) {
                            console.error('Ошибка удаления материала: ', error);
                        }
                    });
                }
            };

            function editMaterial(materialId) {
                // Проверка типа материала
                var materialContent = <?php echo json_encode($materialRow['content'], JSON_HEX_APOS); ?>;

                // Установка значений в модальном окне перед открытием
                document.getElementById('editMaterialForm').elements.material_id.value = materialId;
                document.getElementById('editMaterialForm').elements.materialContent.value = materialContent;

                // Открытие модального окна
                $('#editMaterialModal').modal('show');
            };



            $(document).ready(function() {
                // Обработка формы редактирования
                $('#editMaterialForm').submit(function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var url = form.attr('action');
                    var formData = form.serialize();

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: formData,
                        success: function(response) {
                            // Закрываем модальное окно
                            $('#editMaterialModal').modal('hide');

                            // Обновляем текст материала на странице
                            $('#materialContent').text(form.find('textarea[name="materialContent"]').val());
                        },
                        error: function(error) {
                            console.log('Error:', error);
                        }
                    });
                });
            });



            function removeMaterial(materialId) {
                if (confirm('Вы уверены, что хотите удалить этот материал?')) {
                    $.ajax({
                        type: 'POST',
                        url: './database/delete_material.php',
                        data: {
                            material_id: materialId
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                // Перенаправляем пользователя на страницу с уроками (или иную страницу)
                                window.location.href = './lesson_details.php?lesson_id=<?php echo $materialRow['lesson_id']; ?>'; // Замените 'lessons.php' на нужный URL
                            } else {
                                console.error('Ошибка удаления материала: ', result.message);
                            }
                        },
                        error: function(error) {
                            console.error('Ошибка удаления материала: ', error);
                        }
                    });
                }
            }
        </script>
<?php
    } else {
        echo "<a href='catalog.php' class='btn btn-secondary'>Вернуться к каталогу</a>";
    }
} else {
    echo "Не указан ID материала.";
}

include_once "./base/footer.php";
?>