<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $points_awarded = $_POST['points_awarded'];

    // Добавление или обновление баллов студента
    $addPointsSql = "INSERT INTO student_points (student_id, assignment_id, points_awarded) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE points_awarded = ?";
    $addPointsStmt = $conn->prepare($addPointsSql);
    $addPointsStmt->bind_param("iiii", $student_id, $assignment_id, $points_awarded, $points_awarded);

    if ($addPointsStmt->execute()) {
        echo "Баллы успешно выставлены.";
    } else {
        echo "Ошибка при выставлении баллов.";
    }
} else {
    echo "Недопустимый метод запроса.";
}
?>