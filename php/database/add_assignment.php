<?php
require_once('../base/header.php');
require_once("./db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_id = $_POST['lesson_id'];
    $assignmentTitle = $_POST['assignmentTitle'];
    $assignmentDescription = $_POST['assignmentDescription'];
    $assignmentMaxPoints = $_POST['assignmentMaxPoints'];
    
    $teacher_id = $id; 

    // Обработка файла, если был загружен
    $assignmentFileName = '';
    if (!empty($_FILES['assignmentFile']['name'])) {
        $assignmentFileName = $_FILES['assignmentFile']['name'];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/diploma-project/uploads/'; // Используйте DOCUMENT_ROOT
        $uploadPath = $uploadDir . $assignmentFileName;
        move_uploaded_file($_FILES['assignmentFile']['tmp_name'], $uploadPath);
    }
    $createdAt = date("Y-m-d H:i:s"); // Текущая дата и время

    // Подготовленный запрос для избежания SQL-инъекций
    $assignmentSql = $conn->prepare("INSERT INTO assignments (lesson_id, teacher_id, title, description, created_at, file_path, points) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $assignmentSql->bind_param("iissssi", $lesson_id, $teacher_id, $assignmentTitle, $assignmentDescription, $createdAt, $assignmentFileName, $assignmentMaxPoints);
    

    if ($assignmentSql->execute()) {
        // Успешно добавлено задание
        header("Location: ../../../diploma-project/php/lesson_details.php?lesson_id=$lesson_id"); 
        exit();
    } else {
        // Ошибка при добавлении задания
        echo "Ошибка: " . $assignmentSql->error;
    }

    $assignmentSql->close();
}

?>
