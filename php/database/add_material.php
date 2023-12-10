<?php
include_once "../base/header.php";
include_once "../database/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $lesson_id = $_POST['lesson_id'];
    $material_type = $_POST['materialType'];

    // Общие поля для всех типов материалов
    $common_fields = array(
        'lesson_id' => $lesson_id,
        'type' => $material_type
    );

    // В зависимости от типа материала выбираем дополнительные поля
    if ($material_type == 'text') {
        $text_fields = array(
            'content' => $_POST['textMaterialContent']
        );

        $material_data = array_merge($common_fields, $text_fields);
    } elseif ($material_type == 'test') {
        $test_fields = array(
            'question' => $_POST['testMaterialQuestion'],
            'options' => implode(', ', $_POST['testMaterialOptions']),
            'correct_answer' => implode(', ', $_POST['correctAnswers'])
        );

        $material_data = array_merge($common_fields, $test_fields);
    }

    // Вставляем данные в базу данных
    $insertSql = "INSERT INTO materials (lesson_id, type, content, question, options, correct_answer)
                  VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertSql);

    if ($stmt) {
        // Привязываем параметры
        $stmt->bind_param('ssssss', $material_data['lesson_id'], $material_data['type'],
            $material_data['content'], $material_data['question'], $material_data['options'], $material_data['correct_answer']);

        // Выполняем запрос
        $stmt->execute();

        // Закрываем запрос
        $stmt->close();
        
        // Редирект на страницу урока после добавления материала
        header("Location: ../../../diploma-project/php/lesson_details.php?lesson_id=$lesson_id");
        exit();
    } else {
        echo "Ошибка при подготовке запроса: " . $conn->error;
    }
} else {
    echo "Некорректный метод запроса.";
}

include_once "./base/footer.php";
?>
