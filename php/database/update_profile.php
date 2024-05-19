<?php
require_once("./db.php");

$name = $_POST['name'];
$female = $_POST['female'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = ""; // Инициализируем переменную $sql

// Проверяем, является ли пользователь студентом
$sql = "SELECT * FROM `student` WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['id'] = $row['id'];
    $user_id = $_SESSION['id'];
    $sql = "UPDATE `student` SET name='$name', female='$female', email='$email', password='$password'";
} else {
    // Если пользователь не студент, проверяем, является ли он преподавателем
    $sql = "SELECT * FROM `teacher` WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $user_id = $_SESSION['id'];
        $sql = "UPDATE `teacher` SET name='$name', female='$female', email='$email', password='$password'";
    }
}

// Обработка загрузки изображения
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "";  // Измените на ваш желаемый каталог

    // Убедимся, что каталог существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadFile = $uploadDir . basename($_FILES['profile_image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Проверяем, является ли загруженный файл изображением
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Только JPG, JPEG, PNG и GIF файлы разрешены.";
        exit();
    }

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile;

        // Добавляем путь к изображению к SQL-запросу
        $sql .= ", image='$imagePath' ";
    } else {
        echo "Ошибка при загрузке файла.";
        exit();
    }
} elseif ($_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    echo "Произошла ошибка при загрузке файла.";
    exit();
}

// Добавляем WHERE-условие для завершения SQL-запроса
$sql .= " WHERE id=$user_id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../catalog.php");
} else {
    echo "Ошибка при обновлении данных профиля: " . $conn->error;
}
?>
