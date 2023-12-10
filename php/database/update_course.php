<?php
require_once("./db.php");

$course_id = $_POST['course_id'];
$title = $_POST['title'];
$description = $_POST['description'];


$sql = "SELECT * FROM `courses` WHERE id = '$course_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $sql = "UPDATE `courses` SET title='$title', description='$description'  WHERE id=$course_id";
} 

if ($conn->query($sql) === TRUE) {
    header("Location: ../catalog.php");
} else {
    echo "Ошибка при обновлении данных курса: " . $conn->error;
}