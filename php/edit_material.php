<?php
include_once "../php/database/db.php";

// Проверяем, был ли запрос методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из тела запроса
    $postData = json_decode(file_get_contents("php://input"), true);

    // Проверяем наличие ключа material_id в данных запроса
    if (isset($postData['material_id'])) {
        $material_id = $postData['material_id'];
        
        // Проверяем права доступа для редактирования материала
        $canEdit = canEditMaterial($material_id, $teacher_id);

        if ($canEdit) {
            // Ваши действия по редактированию материала

            // Пример: обновление текстового содержания материала
            if (isset($postData['text_content'])) {
                $text_content = $postData['text_content'];
                $updateTextSql = "UPDATE materials SET text_content = '$text_content' WHERE id = '$material_id'";
                $conn->query($updateTextSql);
            }

            // Пример: обновление вариантов ответов и правильного ответа для теста
            if (isset($postData['options']) && isset($postData['correct_answer'])) {
                $options = implode(",", $postData['options']);
                $correct_answer = $postData['correct_answer'];
                $updateTestSql = "UPDATE materials SET options = '$options', correct_answer = '$correct_answer' WHERE id = '$material_id'";
                $conn->query($updateTestSql);
            }

            echo json_encode(['success' => 'Материал успешно отредактирован']);
        } else {
            echo json_encode(['error' => 'У вас нет прав для редактирования этого материала']);
        }
    } else {
        echo json_encode(['error' => 'Отсутствует material_id в данных запроса']);
    }
} else {
    echo json_encode(['error' => 'Неверный метод запроса']);
}

// Функция проверки прав доступа для редактирования материала
function canEditMaterial($material_id, $teacher_id) {
    // Реализуйте логику, которая проверяет, может ли учитель редактировать данный материал
    // Верните true, если учитель может редактировать материал, и false в противном случае
    // Пример: проверка, что учитель является создателем курса, к которому относится материал
    global $conn;
    $checkTeacherSql = "SELECT * FROM materials 
                        JOIN lessons ON materials.lesson_id = lessons.id 
                        JOIN modules ON lessons.module_id = modules.id 
                        JOIN courses ON modules.course_id = courses.id 
                        WHERE materials.id = '$material_id' AND courses.teacher_id = '$teacher_id'";
    $result = $conn->query($checkTeacherSql);

    return $result->num_rows > 0;
}
?>
