<?php
require_once("./db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    $materialId = $requestData['material_id'];
    $points = $requestData['points'];
    $isCorrect = $requestData['is_correct']; // Значение true или false
    $studentId = $requestData['student_id'];

    // Получение максимального значения attempts из таблицы materials
    $getAttemptsSql = "SELECT attempts FROM materials WHERE id = ?";
    $stmtAttempts = $conn->prepare($getAttemptsSql);
    $stmtAttempts->bind_param("i", $materialId);
    $stmtAttempts->execute();

    $resultAttempts = $stmtAttempts->get_result();

    if ($resultAttempts->num_rows > 0) {
        $row = $resultAttempts->fetch_assoc();
        $maxAttempts = $row['attempts'];

        // Проверка существования записи с указанным material_id и student_id
        $checkProgressSql = "SELECT * FROM progress WHERE material_id = ? AND student_id = ?";
        $stmtCheck = $conn->prepare($checkProgressSql);
        $stmtCheck->bind_param("ii", $materialId, $studentId);
        $stmtCheck->execute();

        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Запись существует
            $progressRow = $resultCheck->fetch_assoc();
            $attemptsLeft = $progressRow['attempts_left'];

            if (!$isCorrect) {
                // Пользователь ответил неверно, уменьшаем число попыток
                $attemptsLeft--;

                // Обновление записи
                $updateProgressSql = "UPDATE progress SET attempts_left = ? WHERE material_id = ? AND student_id = ?";
                $stmtUpdate = $conn->prepare($updateProgressSql);
                $stmtUpdate->bind_param("iii", $attemptsLeft, $materialId, $studentId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }
        } else {
            // Запись не существует
            if (!$isCorrect) {
                // Пользователь ответил неверно, создаем новую запись
                $attemptsLeft1 = $maxAttempts - 1;
                $insertProgressSql = "INSERT INTO progress (student_id, material_id, attempts_left) VALUES (?, ?, ?)";
                $stmtInsert = $conn->prepare($insertProgressSql);
                $stmtInsert->bind_param("iii", $studentId, $materialId, $attemptsLeft1);
                $stmtInsert->execute();
                $stmtInsert->close();
            } elseif ($isCorrect) {
                // Пользователь ответил верно, создаем новую запись с максимальным количеством попыток
                $insertProgressSql = "INSERT INTO progress (student_id, material_id, attempts_left) VALUES (?, ?, ?)";
                $stmtInsert = $conn->prepare($insertProgressSql);
                $stmtInsert->bind_param("iii", $studentId, $materialId, $maxAttempts);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        }

        $stmtCheck->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Attempts not found for the specified material']);
    }

    $stmtAttempts->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
