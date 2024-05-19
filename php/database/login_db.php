<?php
session_start();
require_once("./db.php");

$email = $_POST['email'];
$password = $_POST['password'];

// Избегаем SQL инъекций используя подготовленные запросы
$stmt = $conn->prepare("SELECT * FROM `student` WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Проверяем, есть ли пользователь в таблице студентов
if ($result->num_rows == 0) {
    $stmt = $conn->prepare("SELECT * FROM `teacher` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Проверяем пароль
    if (password_verify($password, $row['password'])) {
        // Устанавливаем сессионные переменные
        $_SESSION['role'] = $row['role'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['female'] = $row['female'];
        $_SESSION['image'] = $row['image'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['id'] = $row['id'];
        $_SESSION['image'] = $row['image'];
        header("Location: ../catalog.php");
        exit();
    } else {
        // Пароль не совпадает
        $_SESSION['error_message'] = "Неверный email или пароль";
        header("Location: ../login.php");
        exit();
    }
} else {
    // Пользователь не найден
    $_SESSION['error_message'] = "Неверный email или пароль";
    header("Location: ../login.php");
    exit();
}
?>
