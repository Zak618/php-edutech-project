<?php
include_once "../../diploma-project/php/base/header.php";
include_once "../php/database/db.php";



if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];


    $materialSql = "SELECT * FROM materials WHERE id = '$material_id'";
    $materialResult = $conn->query($materialSql);
    
    if ($materialResult->num_rows > 0) {
        $materialRow = $materialResult->fetch_assoc();
        
?>
        <div class="container mt-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h1 class="card-title">Тестовая задача</h1>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <?php

                    echo "<form id='answerForm'>";
                    echo "<input type='hidden' name='material_id' value='$material_id'>";
                    echo "<p>Вопрос: " . $materialRow['question'] . "</p>";

                    // Варианты ответов как строка, разделенные запятой
                    $options = explode(",", $materialRow['options']);
                    echo "<p>Варианты ответов:</p>";
                    foreach ($options as $index => $option) {
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='checkbox' name='selectedAnswers[]' value='$index' id='option$index'>";
                        echo "<label class='form-check-label' for='option$index'>$option</label>";
                        echo "</div>";
                    }

                    echo "<button type='button' class='btn btn-primary mt-3' onclick='checkAnswers($material_id)'>Ответить</button>";
                    if ($role == 2) { ?>
                        <button class="btn btn-danger" onclick="deleteTest(<?php echo $material_id; ?>)">Удалить тест</button>
                    <?php }

                    echo "</form>";
                    ?>

                    <!-- Окно с результатом -->
                    <div class="mt-3" id="resultContainer"></div>
                </div>
            </div>

            <div class="mt-3">
                <!-- Дополнительная логика для отображения материалов -->
                <?php
                $lesson_id = $materialRow['lesson_id'];
                $relatedMaterialsSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id' AND id != '$material_id'";
                $relatedMaterialsResult = $conn->query($relatedMaterialsSql);

                if ($relatedMaterialsResult->num_rows > 0) {
                    echo "<h4>Дополнительные материалы:</h4>";
                    echo "<ul>";
                    while ($relatedMaterialRow = $relatedMaterialsResult->fetch_assoc()) {
                        echo "<li>";
                        echo "<a href='";
                        echo ($relatedMaterialRow['type'] == 'text') ? 'text_material.php' : 'test_material.php';
                        echo "?material_id=" . $relatedMaterialRow['id'] . "'>";
                        echo ($relatedMaterialRow['type'] == 'text') ? "Текстовая информация" : "Тестовая задача";
                        echo "</a>";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Нет дополнительных материалов.</p>";
                }
                ?>
            </div>
        </div>

        <script>
            function checkAnswers(materialId) {
                // Отправляем запрос на сервер с использованием Fetch API
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
                    .then(response => response.text()) // Получаем ответ в виде текста
                    .then(resultText => {
                        console.log('Result Text:', resultText);
                        try {
                            var resultData = JSON.parse(resultText);
                            if (resultData.result !== undefined) {
                                if (resultData.result) {
                                    showResultMessage('Верно!', 'alert-success');
                                } else {
                                    showResultMessage('Неверно!', 'alert-danger');
                                }
                            } else if (resultData.error !== undefined) {
                                console.error('Ошибка на сервере:', resultData.error);
                            } else {
                                console.error('Неверный формат данных в ответе:', resultText);
                            }
                        } catch (error) {
                            console.error('Ошибка при парсинге JSON:', error);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при обработке ответа:', error);
                    });
            }

            function getSelectedAnswers() {
                var selectedAnswers = [];
                var checkboxes = document.querySelectorAll('input[name="selectedAnswers[]"]:checked');
                checkboxes.forEach(function(checkbox) {
                    selectedAnswers.push(checkbox.value);
                });
                return selectedAnswers;
            }

            function showResultMessage(message, alertClass) {
                var resultMessage = document.createElement('div');
                resultMessage.className = 'alert ' + alertClass;
                resultMessage.textContent = message;

                var resultContainer = document.getElementById('resultContainer');
                resultContainer.innerHTML = '';
                resultContainer.appendChild(resultMessage);
            }

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