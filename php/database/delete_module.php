<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["module_id"])) {
    // Получаем идентификатор модуля из запроса
    $module_id = $_GET["module_id"];

    // Подготавливаем запрос для удаления уроков, связанных с модулем
    $deleteLessonsSql = "DELETE FROM lessons WHERE id = '$module_id'";

    // Подготавливаем запрос для удаления модуля
    $deleteModuleSql = "DELETE FROM modules WHERE id = '$module_id'";

    // Выполняем запросы
    if ($conn->query($deleteLessonsSql) === TRUE && $conn->query($deleteModuleSql) === TRUE) {
        // Модуль и связанные с ним уроки успешно удалены
        header("Location: ../../../diploma-project/php/my_work_courses.php");
        exit();
    } else {
        echo "Ошибка при удалении модуля: " . $conn->error;
    }
} else {
    echo "Неверный метод запроса или не указан идентификатор модуля.";
}

$conn->close();
?>
