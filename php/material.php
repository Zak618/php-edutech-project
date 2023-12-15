<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

        <div class="container mt-5">
            <div class="card mb-3">
                <div class="card-body">
                    <?php
                    if ($materialRow['type'] == 'text') {
                        echo "<h1 class='card-title'>Текстовая информация</h1>";
                        echo "<p>{$materialRow['content']}</p>";
                    } elseif ($materialRow['type'] == 'test') {
                        echo "<h1 class='card-title'>Тестовая задача</h1>";

                        echo "<form id='answerForm'>";
                        echo "<input type='hidden' name='material_id' value='$material_id'>";
                        echo "<p>Вопрос: {$materialRow['question']}</p>";

                        $options = explode(",", $materialRow['options']);
                        echo "<p>Варианты ответов:</p>";
                        foreach ($options as $index => $option) {
                            echo "<div class='form-check'>";
                            echo "<input class='form-check-input' type='checkbox' name='selectedAnswers[]' value='$index' id='option$index'>";
                            echo "<label class='form-check-label' for='option$index'>$option</label>";
                            echo "</div>";
                        }

                        echo "<button type='button' class='btn btn-primary mt-3' onclick='checkAnswers($material_id)'>Ответить</button>";
                        if ($role == 2) {
                            echo "<button class='btn btn-danger' onclick='deleteTest($material_id)'>Удалить тест</button>";
                        }

                        echo "</form>";

                        echo "<div class='mt-3' id='resultContainer'></div>";
                    }
                    ?>
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
