<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["lesson_id"])) {
    // Получаем идентификатор урока из запроса
    $lesson_id = $_GET["lesson_id"];

    // Подготавливаем запрос для удаления урока
    $deleteLessonSql = "DELETE FROM lessons WHERE id = '$lesson_id'";

    // Выполняем запрос
    if ($conn->query($deleteLessonSql) === TRUE) {
        // Урок успешно удален
        header("Location: ../../../diploma-project/php/my_work_courses.php");
        exit();
    } else {
        echo "Ошибка при удалении урока: " . $conn->error;
    }
} else {
    echo "Неверный метод запроса или не указан идентификатор урока.";
}

$conn->close();
?>
