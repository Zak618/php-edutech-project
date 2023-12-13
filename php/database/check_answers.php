<?php
include_once "../../../diploma-project/php/database/db.php";

// Проверяем, был ли запрос методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из тела запроса
    $postData = json_decode(file_get_contents("php://input"), true);

    try {
        // Проверяем наличие ключа material_id в данных запроса
        if (isset($postData['material_id'])) {
            $material_id = $postData['material_id'];

            // Получаем правильные ответы из базы данных
            $materialSql = "SELECT correct_answer FROM materials WHERE id = '$material_id'";
            $materialResult = $conn->query($materialSql);

            if ($materialResult->num_rows > 0) {
                $materialRow = $materialResult->fetch_assoc();
                $correctAnswers = explode(",", $materialRow['correct_answer']);
                $selectedAnswers = $postData['selectedAnswers'];

                // Преобразуем в числовые значения и вычитаем 1
                $correctAnswers = array_map('intval', $correctAnswers);
                $selectedAnswers = array_map(function ($value) {
                    return intval($value) + 1;
                }, $selectedAnswers);

                // Сравниваем выбранные ответы с правильными
                $result = ($correctAnswers === $selectedAnswers);

                // Выводим результат в формате JSON
                echo json_encode(['result' => $result]);
            } else {
                echo json_encode(['error' => 'Материал не найден.']);
            }
        } else {
            echo json_encode(['error' => 'Отсутствует material_id в данных запроса.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Неверный метод запроса.']);
}
?>
