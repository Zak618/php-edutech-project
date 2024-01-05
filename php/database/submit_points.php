<?php
include_once "../../../diploma-project/php/database/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, были ли переданы данные методом POST
    $material_id = $_POST['material_id'];
    $student_id = $_POST['student_id'];
    $selected_answers = $_POST['selected_answers'];
    $test_attempts = isset($_POST['test_attempts']) ? intval($_POST['test_attempts']) : 0;

    // Получаем информацию о тестовом материале
    $materialSql = "SELECT * FROM materials WHERE id = '$material_id'";
    $materialResult = $conn->query($materialSql);

    if ($materialResult->num_rows > 0) {
        $materialRow = $materialResult->fetch_assoc();
        $lesson_id = $materialRow['lesson_id'];

        // Получаем информацию о прогрессе студента
        $progressSql = "SELECT * FROM progress WHERE student_id = '$student_id' AND lesson_id = '$lesson_id'";
        $progressResult = $conn->query($progressSql);

        if ($progressResult->num_rows > 0) {
            $progressRow = $progressResult->fetch_assoc();

            // Проверяем, не превысил ли студент максимальное количество попыток
            if ($test_attempts < $progressRow['max_attempts']) {
                // Здесь вставьте вашу логику проверки ответов и начисления баллов
                // Ниже приведен пример простой логики, где начисляются баллы за каждый верный ответ

                $correct_answers = explode(',', $materialRow['correct_answer']);
                $earned_points = 0;

                foreach ($selected_answers as $selected_answer) {
                    if (in_array($selected_answer, $correct_answers)) {
                        // За каждый верный ответ начисляется указанное количество баллов
                        $earned_points += $materialRow['points'];
                    }
                }

                // Обновляем информацию о попытках и баллах в таблице progress
                $new_test_attempts = $test_attempts + 1;
                $new_earned_points = $progressRow['earned_points'] + $earned_points;

                $updateProgressSql = "UPDATE progress SET test_attempts = '$new_test_attempts', earned_points = '$new_earned_points' WHERE student_id = '$student_id' AND lesson_id = '$lesson_id'";
                $conn->query($updateProgressSql);

                // Возвращаем успешный ответ с количеством заработанных баллов
                header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'earned_points' => $new_earned_points]);

            } else {
                // Если превышено максимальное количество попыток, возвращаем ошибку
                echo json_encode(['error' => 'Превышено максимальное количество попыток']);
            }
        } else {
            // Если прогресс студента не найден, предполагаем, что у него максимальное количество попыток
            // Создаем запись о прогрессе студента с максимальным количеством попыток
            $max_attempts = $materialRow['attempts']; // Предполагаем, что количество попыток берется из материала
            $initial_earned_points = 0; // Предполагаем, что изначально у студента 0 баллов

            $insertProgressSql = "INSERT INTO progress (student_id, lesson_id, test_attempts, max_attempts, earned_points) VALUES ('$student_id', '$lesson_id', '0', '$max_attempts', '$initial_earned_points')";
            $conn->query($insertProgressSql);

            // Возвращаем успешный ответ с количеством заработанных баллов
            echo json_encode(['status' => 'success', 'earned_points' => $initial_earned_points]);
        }
    } else {
        // Если материал не найден, возвращаем ошибку
        echo json_encode(['error' => 'Материал не найден']);
    }
} else {
    // Если запрос не методом POST, возвращаем ошибку
    echo json_encode(['error' => 'Неверный метод запроса.']);
}

?>
