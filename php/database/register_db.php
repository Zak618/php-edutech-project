<?php
require_once("./db.php");

$name = $_POST['name'];
$female = $_POST['female'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$role = $_POST['role'];
$password = $_POST['password'];

// Шифрование пароля перед сохранением
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Функция для получения случайного аватара
function getRandomAvatar() {
    $avatarId = uniqid();
    $url = "https://api.multiavatar.com/" . $avatarId . ".png";
    $imageData = file_get_contents($url);
    $imageName = $avatarId . ".png";
    
    // Создание директории, если её нет
    if (!is_dir("./database/avatars/")) {
        mkdir("./avatars/", 0777, true);
    }

    file_put_contents("./avatars/" . $imageName, $imageData);
    return $imageName;
}

// Получение случайного аватара
$randomAvatar = getRandomAvatar();

if ($role == 1) {
    $sql = "INSERT INTO `student` (name, female, email, phone, role, password, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $name, $female, $email, $phone, $role, $hashedPassword, $randomAvatar);
} else if ($role == 2) {
    $sql = "INSERT INTO `teacher` (name, female, email, phone, role, password, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $name, $female, $email, $phone, $role, $hashedPassword, $randomAvatar);
}

if ($stmt->execute()) {
    header("Location: ../login.php");
} else {
    echo "Ошибка при добавлении данных: " . $conn->error;
}
$stmt->close();
?>
