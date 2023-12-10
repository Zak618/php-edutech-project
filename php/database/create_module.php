<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, что все необходимые поля существуют
    if (isset($_POST["course_id"]) && isset($_POST["moduleTitle"]) && isset($_POST["moduleDescription"])) {
        // Получаем данные из формы
        $course_id = $_POST["course_id"];
        $moduleTitle = $_POST["moduleTitle"];
        $moduleDescription = $_POST["moduleDescription"];

        // Подготавливаем запрос для вставки нового модуля
        $sql = "INSERT INTO modules (course_id, title, description) VALUES ('$course_id', '$moduleTitle', '$moduleDescription')";

        // Выполняем запрос
        if ($conn->query($sql) === TRUE) {
            // Модуль успешно добавлен
            header("Location: ../../../diploma-project/php/my_work_courses.php");
            exit();
        } else {
            echo "Ошибка: " . $conn->error;
        }
    } else {
        echo "Не все обязательные поля заполнены.";
    }
} else {
    echo "Неверный метод запроса.";
}

$conn->close();
?>
