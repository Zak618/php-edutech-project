<?php

$servername = 'localhost';
$username = 'rolan';
$password = '123';
$dbname = 'edu';

$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Ошибка подключения");
}

?>