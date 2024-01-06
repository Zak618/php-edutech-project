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

    $attemptsLeft = 0; 

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
                $points = 0;
                // Обновление записи
                $updateProgressSql = "UPDATE progress SET attempts_left = ?, points = ? WHERE material_id = ? AND student_id = ?";
                $stmtUpdate = $conn->prepare($updateProgressSql);
                $stmtUpdate->bind_param("iiii", $attemptsLeft, $points, $materialId, $studentId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                // Обновление записи
                $updateProgressSql = "UPDATE progress SET attempts_left = ?, points = ? WHERE material_id = ? AND student_id = ?";
                $stmtUpdate = $conn->prepare($updateProgressSql);
                $stmtUpdate->bind_param("iiii", $attemptsLeft, $points, $materialId, $studentId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }
        } else {
            // Запись не существует
            if (!$isCorrect) {
                // Пользователь ответил неверно, создаем новую запись
                $attemptsLeft = $maxAttempts - 1;
                $points = 0;
                $insertProgressSql = "INSERT INTO progress (student_id, material_id, points, attempts_left) VALUES (?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($insertProgressSql);
                $stmtInsert->bind_param("iiii", $studentId, $materialId, $points, $attemptsLeft);
                $stmtInsert->execute();
                $stmtInsert->close();
            } elseif ($isCorrect) {
                // Пользователь ответил верно, создаем новую запись с максимальным количеством попыток
                $attemptsLeft = $maxAttempts;
                $insertProgressSql = "INSERT INTO progress (student_id, material_id, points, attempts_left) VALUES (?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($insertProgressSql);
                $stmtInsert->bind_param("iiii", $studentId, $materialId, $points, $attemptsLeft);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        }
        
        $stmtCheck->close();
        echo json_encode(['status' => 'success', 'result' => $isCorrect, 'attempts_left' => $attemptsLeft]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Attempts not found for the specified material']);
    }

    $stmtAttempts->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
