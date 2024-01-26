<?php
include_once "../../../diploma-project/php/database/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, были ли переданы данные методом POST
    $lesson_id = $_POST['lesson_id'];
    $material_type = $_POST['materialType'];

    // В зависимости от типа материала обрабатываем данные
    if ($material_type === 'text') {
        $text_content = $_POST['textMaterialContent'];

        // Добавляем текстовый материал в базу данных
        $insertTextMaterialSql = "INSERT INTO materials (lesson_id, type, content) VALUES ('$lesson_id', 'text', '$text_content')";
        $conn->query($insertTextMaterialSql);
    } elseif ($material_type === 'test') {
        $test_question = $_POST['testMaterialQuestion'];
        $test_options = $_POST['testMaterialOptions'];
        $correct_answers = $_POST['correctAnswers'];
        $test_attempts = isset($_POST['testMaterialAttempts']) ? intval($_POST['testMaterialAttempts']) : 'NULL';  // Используем 'NULL' в виде строки
        $test_material_points = isset($_POST['testMaterialPoints']) ? intval($_POST['testMaterialPoints']) : 0;

        // Преобразуем индексы ответов (начиная с 1) в числа и вычитаем 1
        $correct_answers = array_map(function ($value) {
            return intval($value);
        }, $correct_answers);

        // Сериализуем массив в строку для хранения в базе данных
        $serialized_options = implode(",", $test_options);
        $serialized_correct_answers = implode(",", $correct_answers);

        // Добавляем тестовый материал в базу данных
        $insertTestMaterialSql = "INSERT INTO materials (lesson_id, type, question, options, correct_answer, attempts, points) VALUES ('$lesson_id', 'test', '$test_question', '$serialized_options', '$serialized_correct_answers', $test_attempts, '$test_material_points')";
        $conn->query($insertTestMaterialSql);
    } elseif ($material_type === 'video') {
        // Обработка видеоматериала по ссылке
        if (!empty($_POST['videoLink'])) {
            // Если предоставлена ссылка на видео, добавляем видеоматериал в базу данных
            $videoLink = $_POST['videoLink'];
            $insertVideoMaterialSql = "INSERT INTO materials (lesson_id, type, video_link) VALUES ('$lesson_id', 'video', '$videoLink')";
            if ($conn->query($insertVideoMaterialSql)) {
                // Перенаправляем пользователя обратно на страницу урока
                header("Location: ../../../diploma-project/php/lesson_details.php?lesson_id=$lesson_id");
                exit;
            } else {
                echo json_encode(['error' => 'Ошибка при добавлении видеоматериала в базу данных: ' . $conn->error]);
                exit;  // Добавляем exit после отправки ошибки
            }
        } else {
            echo json_encode(['error' => 'Не предоставлена ссылка на видео.']);
            exit;  // Добавляем exit после отправки ошибки
        }
    }

    // Перенаправляем пользователя обратно на страницу урока
    header("Location: ../../../diploma-project/php/lesson_details.php?lesson_id=$lesson_id");
    exit;  // Добавляем exit после успешного завершения
} else {
    // Если запрос не методом POST, возвращаем ошибку
    echo json_encode(['error' => 'Неверный метод запроса.']);
    exit;  // Добавляем exit после отправки ошибки
}
?>
