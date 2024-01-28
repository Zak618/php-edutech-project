<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $edit_points = $_POST['edit_points'];

    // Обновление баллов студента
    $editPointsSql = "UPDATE student_points SET points_awarded = ? WHERE assignment_id = ? AND student_id = ?";
    $editPointsStmt = $conn->prepare($editPointsSql);
    $editPointsStmt->bind_param("iii", $edit_points, $assignment_id, $student_id);

    if ($editPointsStmt->execute()) {
        echo "Баллы успешно отредактированы.";
    } else {
        echo "Ошибка при редактировании баллов.";
    }
} else {
    echo "Недопустимый метод запроса.";
}
?>