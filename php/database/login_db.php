<?php
session_start();

require_once("./db.php");

$email = $_POST['email'];
$password = $_POST['password'];


$sql = "SELECT * FROM `student` WHERE email = '$email' AND password = '$password'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "SELECT * FROM `teacher` WHERE email = '$email' AND password = '$password'";
}

$result1 = $conn->query($sql);


if ($result1->num_rows > 0) {
    $row = $result1->fetch_assoc();
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
    // Ошибка авторизации
    $_SESSION['error_message'] = "Неверный email или пароль";
    header("Location: ../login.php");
}

?>