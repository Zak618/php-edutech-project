<?php
require_once("./db.php");

$name = $_POST['name'];
$female = $_POST['female'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$role = $_POST['role'];
$password = $_POST['password'];

if ($role == 1) {
    $sql = "INSERT INTO `student` (name, female, email, phone, role, password) VALUES ('$name', '$female', '$email', '$phone', '$role', '$password')";
} else if ($role == 2) {
    $sql = "INSERT INTO `teacher` (name, female, email, phone, role, password) VALUES ('$name', '$female', '$email', '$phone', '$role', '$password')";
}


if ($conn->query($sql) === TRUE) {
    header("Location: ../login.php");
} else {
    echo "Ошибка при добавлении данных: " . $conn->error;
}
