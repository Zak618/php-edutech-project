<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, что все необходимые поля существуют
    if (isset($_POST["module_id"]) && isset($_POST["lessonTitle"])) {
        // Получаем данные из формы
        $module_id = $_POST["module_id"];
        $lessonTitle = $_POST["lessonTitle"];

        // Подготавливаем запрос для вставки нового урока
        $sql = "INSERT INTO lessons (module_id, title) VALUES ('$module_id', '$lessonTitle')";

        // Выполняем запрос
        if ($conn->query($sql) === TRUE) {
            // Урок успешно добавлен
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
