<?php
require_once("./db.php");

$course_id = $_POST['course_id'];
$title = $_POST['title'];
$description = $_POST['description'];
$category_id = isset($_POST['category']) ? $_POST['category'] : null;

// Обработка загрузки изображения
if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "./images/courses/";

    // Убедимся, что каталог существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadFile = $uploadDir . basename($_FILES['course_image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Проверяем, является ли загруженный файл изображением
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Только JPG, JPEG, PNG и GIF файлы разрешены.";
        exit();
    }

    if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile;

        // Добавляем путь к изображению к SQL-запросу
        $sql = "UPDATE `courses` SET title='$title', description='$description', image='$imagePath' WHERE id=$course_id";
    } else {
        echo "Ошибка при загрузке файла.";
        exit();
    }
} else {
    // Если файл не был загружен, просто обновляем информацию о курсе без изображения
    $sql = "UPDATE courses SET title = '$title', description = '$description', category_id = '$category_id' WHERE id = '$course_id'";
}

if ($conn->query($sql) === TRUE) {
    header("Location: ../catalog.php");
} else {
    echo "Ошибка при обновлении данных курса: " . $conn->error;
}
?>
