<?php
require_once("./db.php");

$name = $_POST['name'];
$female = $_POST['female'];
$email = $_POST['email'];
$password = $_POST['password'];



$sql = "SELECT * FROM `student` WHERE email = '$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['id'] = $row['id'];
    $user_id = $_SESSION['id'];
    $sql = "UPDATE `student` SET name='$name', female='$female', email='$email', password='$password' WHERE id=$user_id";
} else {
    $sql = "SELECT * FROM `teacher` WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $user_id = $_SESSION['id'];
        $sql = "UPDATE `teacher` SET name='$name', female='$female', email='$email', password='$password' WHERE id=$user_id";
}
}


if ($conn->query($sql) === TRUE) {
    header("Location: ../catalog.php");
} else {
    echo "Ошибка при обновлении данных профиля: " . $conn->error;
}