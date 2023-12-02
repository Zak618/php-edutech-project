<?php
require_once("./db.php");


$title = $_POST['title'];
$description = $_POST['description'];
$teacher_id = $_POST['teacher_id'];

// Подготовка и выполнение запроса на добавление курса в базу данных
$query = "INSERT INTO courses (title, description, teacher_id) VALUES ('$title', '$description', '$teacher_id')";
$result = $conn->query($query);

if ($result) {
    // Курс успешно добавлен
    echo "Курс успешно создан";
} else {
    // Ошибка при добавлении курса
    echo "Ошибка при создании курса";
}

$conn->close();
?>